/**
 * DARK CT - THEME OPTIMIZER
 * Optimización y funcionalidades modernas para el tema oscuro
 */

class ThemeOptimizer {
    constructor() {
        this.init();
    }

    init() {
        this.setupPreloader();
        this.setupLazyLoading();
        this.setupAnimations();
        this.setupPerformanceOptimizations();
        this.setupResponsiveOptimizations();
        this.setupAccessibility();
        this.setupNotifications();
    }

    /**
     * Configurar preloader global
     */
    setupPreloader() {
        // Crear preloader si no existe
        if (!document.getElementById('global-preloader')) {
            const preloader = document.createElement('div');
            preloader.id = 'global-preloader';
            preloader.innerHTML = `
                <div class="preloader-content">
                    <div class="preloader-spinner"></div>
                    <div class="preloader-text">Cargando Dark CT...</div>
                </div>
            `;
            document.body.appendChild(preloader);
        }

        // Ocultar preloader cuando la página esté lista
        window.addEventListener('load', () => {
            setTimeout(() => {
                const preloader = document.getElementById('global-preloader');
                if (preloader) {
                    preloader.classList.add('hidden');
                    setTimeout(() => {
                        preloader.remove();
                    }, 500);
                }
            }, 1000);
        });
    }

    /**
     * Configurar lazy loading para imágenes y elementos
     */
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const lazyElements = document.querySelectorAll('.lazy-load');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        element.classList.add('loaded');
                        observer.unobserve(element);
                    }
                });
            });

            lazyElements.forEach(element => {
                imageObserver.observe(element);
            });
        } else {
            // Fallback para navegadores sin IntersectionObserver
            document.querySelectorAll('.lazy-load').forEach(element => {
                element.classList.add('loaded');
            });
        }
    }

    /**
     * Configurar animaciones suaves
     */
    setupAnimations() {
        // Animar elementos al hacer scroll
        if ('IntersectionObserver' in window) {
            const animateElements = document.querySelectorAll('.animate-on-scroll');
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fadeInUp');
                    }
                });
            }, { threshold: 0.1 });

            animateElements.forEach(element => {
                animationObserver.observe(element);
            });
        }

        // Efecto hover para cards
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });

        // Efecto ripple para botones
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', (e) => {
                this.createRippleEffect(e, button);
            });
        });
    }

    /**
     * Crear efecto ripple en botones
     */
    createRippleEffect(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;

        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    /**
     * Optimizaciones de rendimiento
     */
    setupPerformanceOptimizations() {
        // Debounce para búsquedas
        this.debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // Throttle para scroll
        this.throttle = (func, limit) => {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        };

        // Optimizar scroll
        let ticking = false;
        const updateScroll = () => {
            // Lógica de scroll optimizada
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateScroll);
                ticking = true;
            }
        });

        // Preload de recursos críticos
        this.preloadCriticalResources();
    }

    /**
     * Precargar recursos críticos
     */
    preloadCriticalResources() {
        const criticalResources = [
            'static/v4/css/main.css',
            'static/v4/css/dark-theme.css',
            'static/v4/css/components.css'
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource;
            link.as = 'style';
            document.head.appendChild(link);
        });
    }

    /**
     * Optimizaciones responsive
     */
    setupResponsiveOptimizations() {
        // Detectar cambios de tamaño de pantalla
        const resizeHandler = this.throttle(() => {
            this.updateResponsiveElements();
        }, 250);

        window.addEventListener('resize', resizeHandler);
        this.updateResponsiveElements();
    }

    /**
     * Actualizar elementos responsive
     */
    updateResponsiveElements() {
        const width = window.innerWidth;
        
        // Ajustar grid de estadísticas
        const statsGrid = document.querySelector('.dashboard-stats');
        if (statsGrid) {
            if (width < 768) {
                statsGrid.style.gridTemplateColumns = '1fr';
            } else if (width < 1024) {
                statsGrid.style.gridTemplateColumns = 'repeat(2, 1fr)';
            } else {
                statsGrid.style.gridTemplateColumns = 'repeat(auto-fit, minmax(250px, 1fr))';
            }
        }

        // Ajustar navegación móvil
        const navbar = document.querySelector('.navbar');
        if (navbar && width < 768) {
            this.setupMobileNavigation();
        }
    }

    /**
     * Configurar navegación móvil
     */
    setupMobileNavigation() {
        const navbar = document.querySelector('.navbar');
        if (!navbar || navbar.querySelector('.mobile-menu-toggle')) return;

        const toggleButton = document.createElement('button');
        toggleButton.className = 'mobile-menu-toggle';
        toggleButton.innerHTML = '☰';
        toggleButton.style.cssText = `
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            display: none;
        `;

        if (window.innerWidth < 768) {
            toggleButton.style.display = 'block';
        }

        navbar.appendChild(toggleButton);

        toggleButton.addEventListener('click', () => {
            const navMenu = navbar.querySelector('.nav-menu');
            if (navMenu) {
                navMenu.classList.toggle('active');
            }
        });
    }

    /**
     * Configurar accesibilidad
     */
    setupAccessibility() {
        // Navegación por teclado
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });

        // ARIA labels para elementos interactivos
        document.querySelectorAll('.btn').forEach(button => {
            if (!button.getAttribute('aria-label')) {
                button.setAttribute('aria-label', button.textContent.trim());
            }
        });

        // Focus visible para elementos
        document.querySelectorAll('button, a, input, select, textarea').forEach(element => {
            element.addEventListener('focus', () => {
                element.classList.add('focus-visible');
            });
            element.addEventListener('blur', () => {
                element.classList.remove('focus-visible');
            });
        });
    }

    /**
     * Sistema de notificaciones
     */
    setupNotifications() {
        this.notificationContainer = document.createElement('div');
        this.notificationContainer.className = 'notification-container';
        document.body.appendChild(this.notificationContainer);
    }

    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-header">
                <div class="notification-title">${this.getNotificationTitle(type)}</div>
                <button class="notification-close">&times;</button>
            </div>
            <div class="notification-message">${message}</div>
        `;

        this.notificationContainer.appendChild(notification);

        // Animar entrada
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Auto-remover
        setTimeout(() => {
            this.hideNotification(notification);
        }, duration);

        // Botón de cerrar
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.hideNotification(notification);
        });
    }

    /**
     * Ocultar notificación
     */
    hideNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    /**
     * Obtener título de notificación
     */
    getNotificationTitle(type) {
        const titles = {
            success: 'Éxito',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };
        return titles[type] || 'Notificación';
    }

    /**
     * Mostrar loading en elementos
     */
    showLoading(element) {
        element.classList.add('loading');
        element.disabled = true;
    }

    /**
     * Ocultar loading en elementos
     */
    hideLoading(element) {
        element.classList.remove('loading');
        element.disabled = false;
    }

    /**
     * Crear skeleton loading
     */
    createSkeleton(type = 'text', count = 1) {
        const skeletons = [];
        for (let i = 0; i < count; i++) {
            const skeleton = document.createElement('div');
            skeleton.className = `skeleton skeleton-${type}`;
            skeletons.push(skeleton);
        }
        return skeletons;
    }
}

// CSS adicional para animaciones
const additionalCSS = `
@keyframes ripple {
    0% {
        transform: scale(0);
        opacity: 1;
    }
    100% {
        transform: scale(4);
        opacity: 0;
    }
}

.keyboard-navigation *:focus {
    outline: 2px solid var(--accent-cyan) !important;
    outline-offset: 2px;
}

.focus-visible {
    outline: 2px solid var(--accent-cyan) !important;
    outline-offset: 2px;
}

.mobile-menu-toggle {
    display: none;
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: block !important;
    }
    
    .nav-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--gradient-card);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius);
        padding: 1rem;
        box-shadow: var(--shadow-card);
    }
    
    .nav-menu.active {
        display: block;
    }
}
`;

// Inyectar CSS adicional
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeOptimizer = new ThemeOptimizer();
    });
} else {
    window.themeOptimizer = new ThemeOptimizer();
}

// Exportar para uso global
window.ThemeOptimizer = ThemeOptimizer;
