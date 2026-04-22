// ============================================================
// MAIN.JS — CoffeeBells & Home
// JS Vanilla + Bootstrap 5
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ── 1. LOADER DE PÁGINA ──────────────────────────────────
    const loader = document.getElementById('page-loader');
    if (loader) {
        window.addEventListener('load', () => {
            setTimeout(() => {
                loader.classList.add('loader-hidden');
                setTimeout(() => loader.remove(), 500);
            }, 800);
        });
    }

    // ── 2. AOS - Animaciones al hacer scroll ─────────────────
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
        });
    }

    // ── 3. NAVBAR - Efecto scroll ────────────────────────────
    const navbar = document.getElementById('mainNavbar');
    function handleNavbarScroll() {
        if (!navbar) return;
        if (window.scrollY > 80) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
    window.addEventListener('scroll', handleNavbarScroll);
    handleNavbarScroll();

    // ── 4. BOTÓN VOLVER ARRIBA ───────────────────────────────
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', () => {
            backToTop.classList.toggle('visible', window.scrollY > 400);
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ── 5. SCROLL SUAVE ──────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── 6. BUSCADOR GLOBAL ───────────────────────────────────
    const searchToggle = document.getElementById('searchToggle');
    const searchBar    = document.getElementById('globalSearchBar');
    const searchClose  = document.getElementById('searchClose');
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', () => {
            const isVisible = searchBar.style.display !== 'none';
            searchBar.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) searchBar.querySelector('input').focus();
        });
    }
    if (searchClose && searchBar) {
        searchClose.addEventListener('click', () => {
            searchBar.style.display = 'none';
        });
    }

    // ── 7. CONTADORES ANIMADOS ───────────────────────────────
    const counters = document.querySelectorAll('.counter-number');
    const countObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target  = parseInt(entry.target.dataset.target);
                const duration = 2000;
                const step    = target / (duration / 16);
                let current   = 0;
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    entry.target.textContent = Math.floor(current).toLocaleString('es-MX');
                }, 16);
                countObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(c => countObserver.observe(c));

    // ── 8. TOAST NOTIFICATION ────────────────────────────────
    window.showToast = function (message, type = 'success') {
        const toastEl  = document.getElementById('liveToast');
        const toastMsg = document.getElementById('toastMessage');
        if (!toastEl || !toastMsg) return;
        toastEl.className = `toast align-items-center border-0 text-white bg-${type === 'success' ? 'success' : 'danger'}`;
        toastMsg.textContent = message;
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
        bsToast.show();
    };

    // ── 9. NEWSLETTER FORM ───────────────────────────────────
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = this.querySelector('[name="email"]').value.trim();
            if (!email) return;
            fetch('/coffeebells/actions/save_contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=newsletter&email=${encodeURIComponent(email)}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('newsletterMsg').innerHTML =
                    `<small class="text-${data.success ? 'success' : 'danger'}">${data.message}</small>`;
                if (data.success) this.reset();
            });
        });
    }

    // ── 10. POPUP PROMOCIÓN (aparece a los 5s, solo una vez por sesión) ───
    const promoModal = document.getElementById('promoModal');
    if (promoModal && !sessionStorage.getItem('promoShown')) {
        setTimeout(() => {
            const bsModal = new bootstrap.Modal(promoModal);
            bsModal.show();
            sessionStorage.setItem('promoShown', '1');
        }, 5000);
    }
    const promoForm = document.getElementById('promoForm');
    if (promoForm) {
        promoForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const name  = this.querySelector('[name="name"]').value;
            const email = this.querySelector('[name="email"]').value;
            fetch('/coffeebells/actions/save_contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=promo_lead&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(promoModal).hide();
                    showToast('¡Listo! Te enviamos tu código al correo 🎉', 'success');
                }
            });
        });
    }

    // ── 11. CHATBOT ──────────────────────────────────────────
    const chatToggle   = document.getElementById('chatbot-toggle');
    const chatWindow   = document.getElementById('chatbot-window');
    const chatClose    = document.getElementById('chatbot-close');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput    = document.getElementById('chatInput');
    const chatSend     = document.getElementById('chatSend');
    const chatBadge    = document.getElementById('chatbotBadge');

    const botReplies = {
        servicios: '🔧 Ofrecemos:<br>• Instalaciones eléctricas & Smart Home<br>• Decoración de interiores<br>• Diseño de jardines y paisajismo<br>• Mantenimiento preventivo<br><br>¿Cuál te interesa?',
        cafe: '☕ <strong>Coffee Bells</strong> es nuestra cafetería premium.<br>Horarios: Lun–Sáb 9:00–20:00, Dom 10:00–15:00<br>Vendemos café en grano, molido y accesorios.<br><br>¿Quieres ver el menú o hacer una reserva?',
        tienda: '🛍 Visita nuestra <a href="/coffeebells/tienda.php">tienda online</a> con productos de jardinería, decoración, iluminación y café.<br><br>¿Buscas algo específico?',
        cotizacion: '📋 Para una cotización gratuita puedes:<br>• Llenar nuestro <a href="/coffeebells/contacto.php">formulario</a><br>• Escribirnos por WhatsApp<br>• Llamarnos al +52 464 123 4567<br><br>¿Qué servicio necesitas?',
        ubicacion: '📍 Estamos en <strong>Salamanca, Guanajuato</strong>.<br>Atendemos a domicilio en toda la zona.<br><br><a href="/coffeebells/contacto.php#mapa">Ver en el mapa</a>',
        horarios: '⏰ <strong>Horarios de atención:</strong><br>Lunes a Sábado: 9:00 – 20:00<br>Domingos: 10:00 – 15:00<br><br>También respondemos WhatsApp fuera de horario.',
        precios: '💰 Nuestros precios son competitivos y transparentes.<br>Solicita una cotización gratuita sin compromiso.<br><br>¿Para qué servicio quieres saber el precio?',
        default: '🤔 No entendí bien tu pregunta, pero puedo ayudarte con estas opciones:'
    };

    function addBotMessage(html, showOptions = false) {
        const div = document.createElement('div');
        div.className = 'msg bot';
        let content = `<div class="msg-bubble">${html}</div>`;
        if (showOptions) {
            content += `<div class="msg-options mt-2">
                <button class="opt-btn" data-opt="servicios">🔧 Servicios</button>
                <button class="opt-btn" data-opt="cafe">☕ Café</button>
                <button class="opt-btn" data-opt="tienda">🛍 Tienda</button>
                <button class="opt-btn" data-opt="cotizacion">📋 Cotizar</button>
                <button class="opt-btn" data-opt="asesor">👤 Asesor</button>
            </div>`;
        }
        div.innerHTML = content;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        bindOptButtons();
    }

    function addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'msg user';
        div.innerHTML = `<div class="msg-bubble">${text}</div>`;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function processMessage(text) {
        const t = text.toLowerCase();
        if (t.includes('servicio') || t.includes('electricidad') || t.includes('decoracion') || t.includes('jardin'))
            return botReplies.servicios;
        if (t.includes('cafe') || t.includes('café') || t.includes('coffee') || t.includes('menu'))
            return botReplies.cafe;
        if (t.includes('tienda') || t.includes('producto') || t.includes('comprar'))
            return botReplies.tienda;
        if (t.includes('cotiz') || t.includes('precio') || t.includes('costo') || t.includes('cuanto'))
            return botReplies.cotizacion;
        if (t.includes('ubicacion') || t.includes('dónde') || t.includes('donde') || t.includes('direcc'))
            return botReplies.ubicacion;
        if (t.includes('horario') || t.includes('hora') || t.includes('abierto'))
            return botReplies.horarios;
        if (t.includes('asesor') || t.includes('humano') || t.includes('persona') || t.includes('whatsapp'))
            return null; // Trigger asesor
        return null;
    }

    function bindOptButtons() {
        document.querySelectorAll('.opt-btn').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true)); // Eliminar listeners duplicados
        });
        document.querySelectorAll('.opt-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const opt = this.dataset.opt;
                addUserMessage(this.textContent);
                setTimeout(() => {
                    if (opt === 'asesor') {
                        document.getElementById('chatLeadForm').style.display = 'block';
                        document.getElementById('chatInputGroup').style.display = 'none';
                        addBotMessage('Para conectarte con un asesor, completa el formulario 👇');
                    } else if (botReplies[opt]) {
                        addBotMessage(botReplies[opt], true);
                    } else {
                        addBotMessage(botReplies.default, true);
                    }
                }, 400);
            });
        });
    }

    if (chatToggle && chatWindow) {
        chatToggle.addEventListener('click', () => {
            const visible = chatWindow.style.display !== 'none';
            chatWindow.style.display = visible ? 'none' : 'flex';
            if (chatBadge) chatBadge.style.display = 'none';
        });
    }
    if (chatClose && chatWindow) {
        chatClose.addEventListener('click', () => { chatWindow.style.display = 'none'; });
    }
    if (chatSend && chatInput) {
        function sendChat() {
            const text = chatInput.value.trim();
            if (!text) return;
            addUserMessage(text);
            chatInput.value = '';
            setTimeout(() => {
                const reply = processMessage(text);
                if (reply) {
                    addBotMessage(reply, true);
                } else if (text.toLowerCase().includes('asesor') || text.toLowerCase().includes('whatsapp')) {
                    document.getElementById('chatLeadForm').style.display = 'block';
                    document.getElementById('chatInputGroup').style.display = 'none';
                    addBotMessage('Para conectarte con un asesor, completa el formulario 👇');
                } else {
                    addBotMessage(botReplies.default, true);
                }
            }, 500);
        }
        chatSend.addEventListener('click', sendChat);
        chatInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendChat(); });
    }

    // Captura de lead desde chatbot
    const saveLeadBtn = document.getElementById('saveLead');
    if (saveLeadBtn) {
        saveLeadBtn.addEventListener('click', () => {
            const name  = document.getElementById('leadName').value.trim();
            const phone = document.getElementById('leadPhone').value.trim();
            if (!name || !phone) { alert('Por favor completa nombre y teléfono.'); return; }
            fetch('/coffeebells/actions/chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `name=${encodeURIComponent(name)}&phone=${encodeURIComponent(phone)}`
            })
            .then(r => r.json())
            .then(() => {
                document.getElementById('chatLeadForm').style.display = 'none';
                document.getElementById('chatInputGroup').style.display = 'flex';
                addBotMessage(`✅ ¡Gracias ${name}! Un asesor te contactará pronto al ${phone}.<br><a href="https://wa.me/524641234567?text=Hola%2C+soy+${encodeURIComponent(name)}" target="_blank" class="btn btn-success btn-sm mt-2"><i class="bi bi-whatsapp me-1"></i>Ir a WhatsApp</a>`);
            });
        });
    }

    // ── 12. GLIGHTBOX para galerías ──────────────────────────
    if (typeof GLightbox !== 'undefined') {
        GLightbox({ selector: '.glightbox', touchNavigation: true });
    }

    // ── 13. WhatsApp flotante - ocultar/mostrar en scroll ────
    const waFloat = document.getElementById('whatsappFloat');
    if (waFloat) {
        window.addEventListener('scroll', () => {
            waFloat.classList.toggle('wa-scrolled', window.scrollY > 300);
        });
    }

    // ── 14. CARRITO: Agregar con AJAX ─────────────────────────
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.id;
            fetch('/coffeebells/actions/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&qty=1`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('✅ Producto agregado al carrito', 'success');
                    // Actualizar badge
                    const badge = document.getElementById('cartCountBadge');
                    if (badge) badge.textContent = data.cart_count;
                    this.classList.add('btn-added');
                    setTimeout(() => this.classList.remove('btn-added'), 1500);
                }
            });
        });
    });

    bindOptButtons();
});