tailwind.config = {
    theme: {
        extend: {
            colors: {
                "saar-red": "#FF0000",
                "saar-blue": "#1E40AF",
                "saar-green": "#059669",
            },
            animation: {
                "slide-in": "slideIn 0.5s ease-out",
                "fade-in": "fadeIn 0.3s ease-out",
                "bounce-in": "bounceIn 0.6s ease-out",
                "pulse-slow": "pulse 2s infinite",
                "slide-down": "slideDown 0.3s ease-out",
            },
            keyframes: {
                slideIn: {
                    "0%": {
                        opacity: "0",
                        transform: "translateX(20px)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateX(0)",
                    },
                },
                fadeIn: {
                    "0%": {
                        opacity: "0",
                    },
                    "100%": {
                        opacity: "1",
                    },
                },
                bounceIn: {
                    "0%": {
                        opacity: "0",
                        transform: "scale(0.9)",
                    },
                    "50%": {
                        transform: "scale(1.05)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "scale(1)",
                    },
                },
                slideDown: {
                    "0%": {
                        opacity: "0",
                        transform: "translateY(-10px)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateY(0)",
                    },
                },
            },
        },
    },
};
