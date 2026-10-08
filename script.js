// ============================================
// MODO ESCURO - ENGLISH EXPLORER
// Versão completa com animações e persistência
// ============================================

(function() {
    'use strict';
    
    // ============================================
    // 1. CONFIGURAÇÕES INICIAIS
    // ============================================
    
    // Chave para localStorage
    const STORAGE_KEY = 'englishExplorerDarkMode';
    
    // Classes CSS
    const DARK_MODE_CLASS = 'dark-mode';
    const THEME_TOGGLE_BTN_ID = 'themeToggleBtn';
    
    // Ícones para o botão
    const ICONS = {
        light: '<i class="fas fa-sun"></i>',
        dark: '<i class="fas fa-moon"></i>'
    };
    
    // Textos para o botão
    const TEXTS = {
        light: 'Modo Claro',
        dark: 'Modo Escuro'
    };
    
    // ============================================
    // 2. FUNÇÕES PRINCIPAIS
    // ============================================
    
    /**
     * Verifica se o modo escuro está ativo
     */
    function isDarkModeActive() {
        return document.body.classList.contains(DARK_MODE_CLASS);
    }
    
    /**
     * Ativa o modo escuro
     */
    function enableDarkMode() {
        document.body.classList.add(DARK_MODE_CLASS);
        localStorage.setItem(STORAGE_KEY, 'true');
        updateToggleButton(true);
        showNotification('🌙 Modo escuro ativado', 'info');
        
        // Disparar evento personalizado
        document.dispatchEvent(new CustomEvent('darkModeChanged', { detail: { enabled: true } }));
    }
    
    /**
     * Desativa o modo escuro
     */
    function disableDarkMode() {
        document.body.classList.remove(DARK_MODE_CLASS);
        localStorage.setItem(STORAGE_KEY, 'false');
        updateToggleButton(false);
        showNotification('☀️ Modo claro ativado', 'info');
        
        // Disparar evento personalizado
        document.dispatchEvent(new CustomEvent('darkModeChanged', { detail: { enabled: false } }));
    }
    
    /**
     * Alterna entre os modos
     */
    function toggleDarkMode() {
        if (isDarkModeActive()) {
            disableDarkMode();
        } else {
            enableDarkMode();
        }
    }
    
    /**
     * Atualiza o botão toggle
     */
    function updateToggleButton(isDark) {
        const toggleBtn = document.getElementById(THEME_TOGGLE_BTN_ID);
        if (!toggleBtn) return;
        
        if (isDark) {
            toggleBtn.innerHTML = `${ICONS.dark} ${TEXTS.dark}`;
            toggleBtn.classList.add('dark-active');
            toggleBtn.classList.remove('light-active');
        } else {
            toggleBtn.innerHTML = `${ICONS.light} ${TEXTS.light}`;
            toggleBtn.classList.add('light-active');
            toggleBtn.classList.remove('dark-active');
        }
    }
    
    /**
     * Cria e insere o botão toggle na navbar
     */
    function createToggleButton() {
        // Verificar se já existe
        if (document.getElementById(THEME_TOGGLE_BTN_ID)) return;
        
        // Criar o botão
        const toggleBtn = document.createElement('button');
        toggleBtn.id = THEME_TOGGLE_BTN_ID;
        toggleBtn.className = 'theme-toggle-btn';
        toggleBtn.setAttribute('aria-label', 'Alternar modo escuro/claro');
        toggleBtn.onclick = toggleDarkMode;
        
        // Verificar se deve estar ativo
        const isDark = isDarkModeActive();
        if (isDark) {
            toggleBtn.innerHTML = `${ICONS.dark} ${TEXTS.dark}`;
            toggleBtn.classList.add('dark-active');
        } else {
            toggleBtn.innerHTML = `${ICONS.light} ${TEXTS.light}`;
            toggleBtn.classList.add('light-active');
        }
        
        // Inserir na navbar
        const navbar = document.querySelector('.navbar .container');
        if (navbar) {
            // Verificar se o user-menu já existe
            let userMenu = navbar.querySelector('.user-menu');
            if (userMenu) {
                userMenu.appendChild(toggleBtn);
            } else {
                // Criar container para o botão
                const btnContainer = document.createElement('div');
                btnContainer.className = 'theme-btn-container';
                btnContainer.appendChild(toggleBtn);
                navbar.appendChild(btnContainer);
            }
        } else {
            // Fallback: inserir no body
            document.body.appendChild(toggleBtn);
        }
    }
    
    /**
     * Sistema de notificações toast
     */
    function showNotification(message, type = 'success') {
        // Verificar se já existe uma notificação
        const existingNotification = document.querySelector('.darkmode-toast');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Criar notificação
        const notification = document.createElement('div');
        notification.className = `darkmode-toast toast-${type}`;
        notification.innerHTML = `
            <div class="toast-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // ============================================
    // 3. SISTEMA DE PREFERÊNCIA DO SISTEMA
    // ============================================
    
    /**
     * Detecta preferência do sistema (dark/light)
     */
    function getSystemPreference() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    
    /**
     * Escuta mudanças na preferência do sistema
     */
    function listenSystemPreference() {
        if (!window.matchMedia) return;
        
        const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        // Para navegadores modernos
        if (darkModeMediaQuery.addEventListener) {
            darkModeMediaQuery.addEventListener('change', (e) => {
                // Só aplicar se não houver preferência salva
                if (localStorage.getItem(STORAGE_KEY) === null) {
                    if (e.matches) {
                        enableDarkMode();
                    } else {
                        disableDarkMode();
                    }
                }
            });
        } 
        // Fallback para navegadores antigos
        else if (darkModeMediaQuery.addListener) {
            darkModeMediaQuery.addListener((e) => {
                if (localStorage.getItem(STORAGE_KEY) === null) {
                    if (e.matches) {
                        enableDarkMode();
                    } else {
                        disableDarkMode();
                    }
                }
            });
        }
    }
    
    
    // ============================================
    // 4. ANIMAÇÕES E TRANSIÇÕES
    // ============================================
    
    /**
     * Adiciona animação suave ao trocar tema
     */
    function addSmoothTransition() {
        // Adicionar classe temporária para animação
        const style = document.createElement('style');
        style.textContent = `
            .theme-transitioning * {
                transition: all 0.3s ease-in-out !important;
            }
        `;
        document.head.appendChild(style);
        
        // Adicionar evento de transição
        document.addEventListener('darkModeChanged', () => {
            document.body.classList.add('theme-transitioning');
            setTimeout(() => {
                document.body.classList.remove('theme-transitioning');
            }, 300);
        });
    }
    
    // ============================================
    // 5. INICIALIZAÇÃO
    // ============================================
    
    /**
     * Inicializa o sistema de modo escuro
     */
    function initDarkMode() {
        // Verificar preferência salva
        const savedPreference = localStorage.getItem(STORAGE_KEY);
        
        if (savedPreference !== null) {
            // Usar preferência salva
            if (savedPreference === 'true') {
                enableDarkMode();
            } else {
                disableDarkMode();
            }
        } else {
            // Usar preferência do sistema
            const systemPrefersDark = getSystemPreference();
            if (systemPrefersDark) {
                enableDarkMode();
            } else {
                disableDarkMode();
            }
        }
        
        // Criar botão toggle
        createToggleButton();
        
        // Escutar preferência do sistema
        listenSystemPreference();
        
        // Adicionar animação suave
        addSmoothTransition();
        
        console.log('🌓 Modo escuro inicializado com sucesso!');
    }
    
    // ============================================
    // 6. EXPORTAR FUNÇÕES (para uso global)
    // ============================================
    
    // Disponibilizar funções globalmente
    window.darkMode = {
        enable: enableDarkMode,
        disable: disableDarkMode,
        toggle: toggleDarkMode,
        isActive: isDarkModeActive,
        showNotification: showNotification
    };
    
    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDarkMode);
    } else {
        initDarkMode();
    }
    
})();