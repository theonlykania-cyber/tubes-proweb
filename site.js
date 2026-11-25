document.addEventListener('DOMContentLoaded', () => {
  const productCards = Array.from(
    document.querySelectorAll('.product-card[data-product-search]')
  );
  const searchForm = document.querySelector('.site-search');

  if (!productCards.length || !searchForm) return;

  const queryInput = searchForm.querySelector('[name="q"]');
  const typeSelect = searchForm.querySelector('[name="type"]');
  const status = document.getElementById('searchStatus');
  const params = new URLSearchParams(window.location.search);

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
