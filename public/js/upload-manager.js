/**
 * Gestionnaire d'upload optimisé avec compression et upload asynchrone
 */
class UploadManager {
    constructor() {
        this.uploadQueue = new Map();
        this.maxFileSize = 5 * 1024 * 1024; // 5MB
        this.chunkSize = 1024 * 1024; // 1MB chunks
        this.maxRetries = 3;
        this.uploadEndpoint = '/api/upload/chunk';
        this.sessionId = this.generateSessionId();
    }

    /**
     * Générer un ID de session unique
     */
    generateSessionId() {
        return 'upload_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Compresser une image
     */
    async compressImage(file, maxWidth = 1920, quality = 0.8) {
        return new Promise((resolve, reject) => {
            if (!file.type.startsWith('image/')) {
                resolve(file);
                return;
            }

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = () => {
                let { width, height } = img;
                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }

                canvas.width = width;
                canvas.height = height;

                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(
                    (blob) => {
                        if (blob.size < file.size) {
                            console.log(`Image compressée: ${file.size} → ${blob.size} bytes`);
                            resolve(new File([blob], file.name, { type: file.type }));
                        } else {
                            resolve(file);
                        }
                    },
                    file.type,
                    quality
                );
            };

            img.onerror = () => reject(new Error('Erreur lors du chargement de l\'image'));
            img.src = URL.createObjectURL(file);
        });
    }

    /**
     * Diviser un fichier en chunks
     */
    createChunks(file) {
        const chunks = [];
        let start = 0;
        let chunkIndex = 0;

        while (start < file.size) {
            const end = Math.min(start + this.chunkSize, file.size);
            const chunk = file.slice(start, end);
            chunks.push({
                index: chunkIndex,
                data: chunk,
                start,
                end,
                total: file.size
            });
            start = end;
            chunkIndex++;
        }

        return chunks;
    }

    /**
     * Uploader un chunk
     */
    async uploadChunk(chunk, fileId, retryCount = 0) {
        const formData = new FormData();
        formData.append('chunk', chunk.data);
        formData.append('fileId', fileId);
        formData.append('chunkIndex', chunk.index);
        formData.append('totalChunks', Math.ceil(chunk.total / this.chunkSize));
        formData.append('sessionId', this.sessionId);

        try {
            const response = await fetch(this.uploadEndpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`Erreur upload chunk: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            if (retryCount < this.maxRetries) {
                console.log(`Tentative ${retryCount + 1}/${this.maxRetries} pour chunk ${chunk.index}`);
                await this.delay(1000 * (retryCount + 1));
                return this.uploadChunk(chunk, fileId, retryCount + 1);
            }
            throw error;
        }
    }

    /**
     * Uploader un fichier complet
     */
    async uploadFile(file, field, onProgress) {
        try {
            const processedFile = await this.compressImage(file);
            
            if (processedFile.size > this.maxFileSize) {
                throw new Error(`Fichier trop volumineux: ${(processedFile.size / 1024 / 1024).toFixed(2)}MB`);
            }

            const fileId = this.generateFileId();
            const chunks = this.createChunks(processedFile);
            const totalChunks = chunks.length;

            this.uploadQueue.set(fileId, {
                file: processedFile,
                field,
                chunks,
                uploadedChunks: 0,
                totalChunks,
                status: 'uploading'
            });

            for (const chunk of chunks) {
                await this.uploadChunk(chunk, fileId);
                
                const uploadInfo = this.uploadQueue.get(fileId);
                uploadInfo.uploadedChunks++;
                
                const progress = (uploadInfo.uploadedChunks / totalChunks) * 100;
                onProgress(progress, fileId);

                await this.delay(100);
            }

            const finalResponse = await this.finalizeUpload(fileId, processedFile, field);
            
            this.uploadQueue.set(fileId, {
                ...this.uploadQueue.get(fileId),
                status: 'completed',
                finalPath: finalResponse.path
            });

            return finalResponse;

        } catch (error) {
            console.error('Erreur upload fichier:', error);
            this.uploadQueue.set(fileId, {
                ...this.uploadQueue.get(fileId),
                status: 'error',
                error: error.message
            });
            throw error;
        }
    }

    /**
     * Finaliser l'upload
     */
    async finalizeUpload(fileId, file, field) {
        const response = await fetch('/api/upload/finalize', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                fileId,
                sessionId: this.sessionId,
                originalName: file.name,
                mimeType: file.type,
                size: file.size,
                field
            })
        });

        if (!response.ok) {
            throw new Error('Erreur lors de la finalisation');
        }

        return await response.json();
    }

    /**
     * Générer un ID de fichier unique
     */
    generateFileId() {
        return 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Délai utilitaire
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Obtenir le statut d'un upload
     */
    getUploadStatus(fileId) {
        return this.uploadQueue.get(fileId);
    }

    /**
     * Annuler un upload
     */
    cancelUpload(fileId) {
        const uploadInfo = this.uploadQueue.get(fileId);
        if (uploadInfo && uploadInfo.status === 'uploading') {
            uploadInfo.status = 'cancelled';
            this.uploadQueue.set(fileId, uploadInfo);
        }
    }

    /**
     * Nettoyer les uploads terminés
     */
    cleanup() {
        for (const [fileId, uploadInfo] of this.uploadQueue) {
            if (uploadInfo.status === 'completed' || uploadInfo.status === 'error') {
                this.uploadQueue.delete(fileId);
            }
        }
    }
}

// Instance globale
window.uploadManager = new UploadManager();
