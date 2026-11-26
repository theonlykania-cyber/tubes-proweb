document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const safeDestinations = new Set(['tambahkan.html', 'profilesaya.html', 'produksaya.html', 'edit.html']);
  const requestedDestination = safeDestinations.has(params.get('next'))
    ? params.get('next')
    : '';
  const loginUrl = (destination = '') => {
    const query = destination ? `?next=${encodeURIComponent(destination)}` : '';
    return `login.html${query}`;
  };

  const authRequest = fetch('danusu_php_backend/auth-status.php', {
    credentials: 'same-origin',
    headers: { Accept: 'application/json' }
  }).then((response) => {
    if (!response.ok) throw new Error('Status sesi tidak tersedia.');
    return response.json();
  }).catch(() => ({ loggedIn: false }));

  authRequest.then((auth) => {
    document.querySelectorAll('.site-profile').forEach((link) => {
      link.style.display = auth.loggedIn ? 'inline-flex' : 'none';
      link.setAttribute('aria-label', auth.loggedIn ? 'Profil Saya' : 'Akun Anda');
      link.title = auth.nama ? `Profil Saya — ${auth.nama}` : 'Profil Saya';

      if (auth.loggedIn && !link.querySelector('.site-profile-label')) {
        const label = document.createElement('span');
        label.className = 'site-profile-label';
        label.textContent = 'Profil Saya';
        link.append(label);
      }
    });

    document.querySelectorAll('.site-login').forEach((link) => {
      link.style.display = auth.loggedIn ? 'none' : '';
    });

    document.querySelectorAll('.site-navbar').forEach((navbar) => {
      navbar.querySelector('.site-logout')?.remove();
      if (!auth.loggedIn) return;

      const logout = document.createElement('a');
      logout.className = 'site-login site-logout';
      logout.href = 'danusu_php_backend/actions/logout.php';
      logout.textContent = 'KELUAR';
      navbar.append(logout);
    });

    const currentPage = window.location.pathname.split('/').pop();
    const privatePages = new Set(['profilesaya.html', 'produksaya.html', 'tambahkan.html', 'edit.html']);

    if (auth.loggedIn && currentPage === 'login.html') {
      window.location.replace(requestedDestination || 'profilesaya.html');
      return;
    }
    if (auth.loggedIn && currentPage === 'akun.html') {
      window.location.replace('profilesaya.html');
      return;
    }
    if (!auth.loggedIn && privatePages.has(currentPage)) {
      window.location.replace(loginUrl(currentPage));
      return;
    }

    document.querySelectorAll('a[data-requires-auth]').forEach((link) => {
      link.href = auth.loggedIn ? 'tambahkan.html' : loginUrl('tambahkan.html');
    });

    const productForm = document.querySelector('.product-form');
    if (productForm) {
      if (!auth.loggedIn) {
        window.location.replace(loginUrl('tambahkan.html'));
        return;
      }
      let csrfInput = productForm.querySelector('[name="csrf"]');
      if (!csrfInput) {
        csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf';
        productForm.append(csrfInput);
      }
      csrfInput.value = auth.csrf || '';
    }

    const deleteForm = document.querySelector('.product-delete-form');
    const productId = params.get('id');
    if (deleteForm && auth.loggedIn && productId && /^[1-9]\d*$/.test(productId)) {
      deleteForm.querySelector('[name="id"]').value = productId;
      deleteForm.querySelector('[name="csrf"]').value = auth.csrf || '';
      deleteForm.hidden = false;
      deleteForm.addEventListener('submit', (event) => {
        if (!window.confirm('Yakin ingin menghapus produk ini? Produk yang dihapus tidak dapat dikembalikan.')) {
          event.preventDefault();
        }
      });
    }
  });

  document.querySelectorAll('form.login-form, form.register-form').forEach((form) => {
    const destination = requestedDestination;
    if (destination) {
      let nextInput = form.querySelector('[name="next"]');
      if (!nextInput) {
        nextInput = document.createElement('input');
        nextInput.type = 'hidden';
        nextInput.name = 'next';
        form.append(nextInput);
      }
      nextInput.value = destination;
    }

    if (form.matches('.login-form')) {
      form.action = 'danusu_php_backend/actions/login.php';
      const registerLink = document.querySelector('.register-prompt a');
      if (registerLink && destination) {
        registerLink.href = `akun.html?next=${encodeURIComponent(destination)}`;
      }
    } else {
      form.action = 'danusu_php_backend/actions/register.php';
      const loginLink = document.querySelector('.register-prompt a');
      if (loginLink && destination) loginLink.href = loginUrl(destination);
    }
  });

  const productCards = Array.from(
    document.querySelectorAll('.product-card[data-product-search]')
  );
  const searchForm = document.querySelector('.site-search');

  if (!productCards.length || !searchForm) return;

  const queryInput = searchForm.querySelector('[name="q"]');
  const typeSelect = searchForm.querySelector('[name="type"]');
  const status = document.getElementById('searchStatus');
  queryInput.value = params.get('q') || '';
  typeSelect.value = params.get('type') || 'all';

  const normalize = (value) => value
    .toLocaleLowerCase('id')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim();

  const filterProducts = () => {
    const query = normalize(queryInput.value);
    const mode = typeSelect.value;
    let visibleCount = 0;

    productCards.forEach((card) => {
      const productText = normalize(card.dataset.productSearch || '');
      const storeText = normalize(card.dataset.storeSearch || '');
      const matches = !query
        || (mode === 'products' && productText.includes(query))
        || (mode === 'stores' && storeText.includes(query))
        || (mode === 'all' && `${productText} ${storeText}`.includes(query));

      card.hidden = !matches;
      if (matches) visibleCount += 1;
    });

    if (status) {
      status.hidden = !query;
      status.textContent = visibleCount
        ? `${visibleCount} produk atau toko ditemukan.`
        : 'Tidak ada produk atau toko yang cocok dengan pencarian.';
    }
  };

  searchForm.addEventListener('submit', (event) => {
    event.preventDefault();
    filterProducts();

    const nextParams = new URLSearchParams();
    if (queryInput.value.trim()) nextParams.set('q', queryInput.value.trim());
    if (typeSelect.value !== 'all') nextParams.set('type', typeSelect.value);
    const queryString = nextParams.toString();
    window.history.replaceState({}, '', `${window.location.pathname}${queryString ? `?${queryString}` : ''}#products`);
    document.getElementById('products')?.scrollIntoView({ behavior: 'smooth' });
  });

  queryInput.addEventListener('input', filterProducts);
  typeSelect.addEventListener('change', filterProducts);
  filterProducts();
});
