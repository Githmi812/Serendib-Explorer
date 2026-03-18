 
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
        
        // Mobile menu close on link click
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
        });

        /* ── login page ── */
         /* ── Tab switch ── */
    function switchTab(tab) {
        const isSign = tab === 'sign';
        document.getElementById('tabSign').classList.toggle('active',  isSign);
        document.getElementById('tabReg').classList.toggle('active',  !isSign);
        document.getElementById('panelSign').classList.toggle('active', isSign);
        document.getElementById('panelReg').classList.toggle('active', !isSign);
        clearAlerts();
    }

    /* ── Eye toggle ── */
    function toggleEye(id, btn) {
        const inp  = document.getElementById(id);
        const show = inp.type === 'password';
        inp.type   = show ? 'text' : 'password';
        btn.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
    }

    /* ── Password strength ── */
    function checkStrength(v) {
        const bar = document.getElementById('sBar');
        const lbl = document.getElementById('sLbl');
        if (!v) { bar.style.width = '0'; lbl.textContent = ''; return; }
        let s = 0;
        if (v.length >= 6)          s++;
        if (v.length >= 10)         s++;
        if (/[A-Z]/.test(v))        s++;
        if (/[0-9]/.test(v))        s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        const lvls = [
            { w:'20%', c:'#e74c3c', t:'Very Weak'  },
            { w:'40%', c:'#e67e22', t:'Weak'        },
            { w:'60%', c:'#f1c40f', t:'Fair'        },
            { w:'80%', c:'#2ecc71', t:'Strong'      },
            { w:'100%',c:'#1e8449', t:'Very Strong' },
        ];
        const l = lvls[Math.min(s - 1, 4)];
        bar.style.width = l.w; bar.style.background = l.c;
        lbl.textContent = l.t; lbl.style.color = l.c;
    }

    /* ── Alerts ── */
    function showAlert(id, msg, type) {
        const icons = { 'is-ok':'fa-check-circle', 'is-err':'fa-times-circle', 'is-warn':'fa-exclamation-triangle' };
        const el = document.getElementById(id);
        el.innerHTML = `<i class="fas ${icons[type]}"></i> ${msg}`;
        el.className = `auth-alert show ${type}`;
    }
    function clearAlerts() {
        document.querySelectorAll('.auth-alert').forEach(e => {
            e.className = 'auth-alert'; e.textContent = '';
        });
    }

    /* ── Button loading ── */
    function setBtn(id, loading, html) {
        const b = document.getElementById(id);
        b.disabled = loading;
        b.innerHTML = loading
            ? '<span><i class="fas fa-spinner fa-spin me-2"></i>Please wait…</span>'
            : `<span>${html}</span>`;
    }

    /* ── SIGN IN ── */
    async function doSignIn() {
        const email = document.getElementById('siEmail').value.trim();
        const pass  = document.getElementById('siPass').value;
        if (!email || !pass)
            return showAlert('alertSign', 'Please fill in both fields.', 'is-warn');
        setBtn('btnSign', true);
        try {
            const r = await fetch('api_login.php', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body: JSON.stringify({ email, password: pass })
            });
            const d = await r.json();
            if (d.success) {
                localStorage.setItem('token', d.token);
                localStorage.setItem('user',  JSON.stringify(d.user));
                showAlert('alertSign', d.message + ' — Redirecting…', 'is-ok');
                setTimeout(() => window.location.href = 'tour.html', 1300);
            } else {
                showAlert('alertSign', d.message, 'is-err');
                setBtn('btnSign', false, '<i class="fas fa-sign-in-alt me-2"></i>Sign In to Serendib');
            }
        } catch {
            showAlert('alertSign', 'Cannot reach server. Is the backend running?', 'is-err');
            setBtn('btnSign', false, '<i class="fas fa-sign-in-alt me-2"></i>Sign In to Serendib');
        }
    }

    /* ── REGISTER ── */
    async function doRegister() {
        const name  = document.getElementById('regName').value.trim();
        const email = document.getElementById('regEmail').value.trim();
        const pass  = document.getElementById('regPass').value;
        const conf  = document.getElementById('regConf').value;
        if (!name || !email || !pass || !conf)
            return showAlert('alertReg', 'Please fill in all fields.', 'is-warn');
        if (pass.length < 6)
            return showAlert('alertReg', 'Password must be at least 6 characters.', 'is-warn');
        if (pass !== conf)
            return showAlert('alertReg', 'Passwords do not match.', 'is-warn');
        setBtn('btnReg', true);
        try {
            const r = await fetch('api_register.php', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body: JSON.stringify({ name, email, password: pass })
            });
            const d = await r.json();
            if (d.success) {
                showAlert('alertReg', d.message + ' Switching to sign in…', 'is-ok');
                setTimeout(() => switchTab('sign'), 1600);
            } else {
                showAlert('alertReg', d.message, 'is-err');
            }
        } catch {
            showAlert('alertReg', 'Cannot reach server. Is the backend running?', 'is-err');
        }
        setBtn('btnReg', false, '<i class="fas fa-user-plus me-2"></i>Create My Account');
    }

    /* ── Enter key ── */
    document.addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        if (document.getElementById('panelSign').classList.contains('active')) doSignIn();
        else doRegister();
    });
