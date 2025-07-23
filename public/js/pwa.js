let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  
  showInstallPromotion();
});

function showInstallPromotion() {
  
  if (window.matchMedia('(display-mode: standalone)').matches) return;
  const div = document.createElement('div');
  div.id = 'pwa-install-prompt';
  div.style.position = 'fixed';
  div.style.bottom = '20px';
  div.style.left = '50%';
  div.style.transform = 'translateX(-50%)';
  div.style.background = '#fff';
  div.style.border = '1px solid #ccc';
  div.style.padding = '16px';
  div.style.zIndex = '10000';
  div.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
  div.innerHTML = '<span>Installer SaarSinistre sur votre écran d\'accueil ?</span> <button id="pwa-install-btn">Installer</button>';
  document.body.appendChild(div);
  document.getElementById('pwa-install-btn').onclick = function() {
    div.remove();
    if (deferredPrompt) {
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then(() => { deferredPrompt = null; });
    }
  };
} 