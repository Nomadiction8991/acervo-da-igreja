// Theme Toggle Script
document.addEventListener('DOMContentLoaded', () => {
  const normalizeCepDigits = (value) => (value ?? '').replace(/\D/g, '').slice(0, 8);
  const formatCep = (value) => {
    const digits = normalizeCepDigits(value);

    if (digits.length <= 5) {
      return digits;
    }

    return `${digits.slice(0, 5)}-${digits.slice(5)}`;
  };

  const syncColorFields = () => {
    document.querySelectorAll('[data-color-field]').forEach((field) => {
      const input = field.querySelector('[data-color-input]');
      const value = field.querySelector('[data-color-value]');

      if (!input || !value) {
        return;
      }

      const sync = () => {
        value.textContent = (input.value || '').toUpperCase();
      };

      sync();
      input.addEventListener('input', sync);
      input.addEventListener('change', sync);
    });
  };

  const toggles = Array.from(document.querySelectorAll('[data-theme-toggle]'));
  const themeMeta = document.querySelector('meta[name="theme-color"]');
  const themeColors = {
    light: '#f4ede2',
    dark: '#0a0d14',
  };

  const syncThemeMeta = (theme) => {
    if (themeMeta) {
      themeMeta.setAttribute('content', themeColors[theme] ?? themeColors.light);
    }
  };

  const setTheme = (theme) => {
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('acervo-igreja-theme', theme);
    syncThemeMeta(theme);
  };

  const initTheme = () => {
    const stored = localStorage.getItem('acervo-igreja-theme');
    const prefered = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const theme = stored ?? prefered;
    document.documentElement.dataset.theme = theme;
    syncThemeMeta(theme);
  };

  const toggleTheme = () => {
    const current = document.documentElement.dataset.theme ?? 'light';
    const next = current === 'light' ? 'dark' : 'light';
    setTheme(next);
  };

  initTheme();
  toggles.forEach(toggle => {
    toggle.addEventListener('click', toggleTheme);
  });

  const menuRoots = Array.from(document.querySelectorAll('[data-menu-root]'));

  const closeMenu = (toggle) => {
    if (toggle) {
      toggle.checked = false;
    }
  };

  menuRoots.forEach(root => {
    const toggle = root.querySelector('[data-menu-toggle]');

    if (!toggle) {
      return;
    }

    root.querySelectorAll('[data-menu-nav-link]').forEach(link => {
      link.addEventListener('click', () => {
        closeMenu(toggle);
      });
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
      return;
    }

    document.querySelectorAll('[data-menu-toggle]').forEach(toggle => {
      closeMenu(toggle);
    });
  });

  const cepForms = Array.from(document.querySelectorAll('[data-cep-lookup-form]'));

  const initCepLookup = (form) => {
    const cepInput = form.querySelector('[data-cep-input]');
    const addressInput = form.querySelector('[data-address-input]');
    const cityInput = form.querySelector('[data-city-input]');
    const stateInput = form.querySelector('[data-state-input]');
    const status = form.querySelector('[data-cep-status]');
    const urlTemplate = form.dataset.cepLookupUrlTemplate;

    if (!cepInput || !addressInput || !cityInput || !stateInput || !urlTemplate) {
      return;
    }

    let lastLookupCep = '';
    let requestSequence = 0;

    const setStatus = (message = '') => {
      if (status) {
        status.textContent = message;
      }
    };

    const fillAddress = (payload) => {
      addressInput.value = payload.endereco ?? '';
      cityInput.value = payload.cidade ?? '';
      stateInput.value = (payload.estado ?? '').toUpperCase();
    };

    const lookupCep = async () => {
      const cep = normalizeCepDigits(cepInput.value);

      if (cep.length !== 8) {
        lastLookupCep = '';
        setStatus('');
        return;
      }

      if (cep === lastLookupCep) {
        return;
      }

      requestSequence += 1;
      const currentRequest = requestSequence;
      setStatus('Buscando CEP...');

      try {
        const response = await fetch(urlTemplate.replace('__CEP__', cep), {
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });
        const payload = await response.json().catch(() => ({}));

        if (currentRequest !== requestSequence) {
          return;
        }

        if (!response.ok) {
          lastLookupCep = '';
          setStatus(payload.message ?? 'Nao foi possivel localizar o CEP.');
          return;
        }

        fillAddress(payload);
        lastLookupCep = cep;
        setStatus('Endereco preenchido automaticamente.');
      } catch (error) {
        if (currentRequest !== requestSequence) {
          return;
        }

        lastLookupCep = '';
        setStatus('Nao foi possivel consultar o CEP agora.');
      }
    };

    cepInput.value = formatCep(cepInput.value);

    cepInput.addEventListener('input', () => {
      cepInput.value = formatCep(cepInput.value);

      if (normalizeCepDigits(cepInput.value).length === 8) {
        lookupCep();
      } else {
        lastLookupCep = '';
        setStatus('');
      }
    });

    cepInput.addEventListener('blur', () => {
      lookupCep();
    });
  };

  cepForms.forEach(initCepLookup);
  syncColorFields();
});
