(() => {
    const navToggle = document.querySelector('.nav-toggle');
    const navigation = document.querySelector('.sidebar');

    if (navToggle && navigation) {
        navToggle.addEventListener('click', () => {
            const expanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!expanded));
            navigation.classList.toggle('nav-open', !expanded);
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'Continue with this action?')) {
                event.preventDefault();
            }
        });
    });

    const liveSearch = document.querySelector('[data-medicine-search]');
    if (liveSearch) {
        const input = liveSearch.querySelector('[data-live-search]');
        const status = liveSearch.querySelector('[data-search-status]');
        const results = liveSearch.querySelector('[data-search-results]');
        let timer;
        let controller;

        input.addEventListener('input', () => {
            window.clearTimeout(timer);
            if (controller) controller.abort();

            const query = input.value.trim();
            results.replaceChildren();

            if (!query) {
                status.textContent = 'Start typing to search your catalog.';
                return;
            }

            status.textContent = 'Searching…';
            timer = window.setTimeout(async () => {
                controller = new AbortController();
                try {
                    const endpoint = new URL(liveSearch.dataset.apiUrl, window.location.href);
                    endpoint.searchParams.set('q', query);
                    const response = await fetch(endpoint, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        signal: controller.signal,
                    });
                    const payload = await response.json();
                    if (!response.ok || !payload.ok) {
                        throw new Error(payload.error || 'Search is temporarily unavailable.');
                    }

                    status.textContent = `${payload.count} result${payload.count === 1 ? '' : 's'} found.`;
                    if (payload.results.length === 0) {
                        const empty = document.createElement('p');
                        empty.className = 'empty-inline';
                        empty.textContent = 'No medicines match this search.';
                        results.append(empty);
                        return;
                    }

                    payload.results.forEach((medicine) => {
                        const row = document.createElement('div');
                        row.className = 'live-result';
                        const details = document.createElement('span');
                        details.className = 'compact-main';
                        const name = document.createElement('strong');
                        name.textContent = medicine.name;
                        const meta = document.createElement('small');
                        meta.textContent = `${medicine.category} · ${medicine.supplier} · Expires ${medicine.expiry_date}`;
                        details.append(name, meta);

                        const stock = document.createElement('span');
                        stock.className = medicine.status === 'low' || medicine.status === 'expired' ? 'quantity-low' : 'live-stock';
                        stock.textContent = `${medicine.stock} units · ${medicine.status_label}`;
                        row.append(details, stock);
                        results.append(row);
                    });
                } catch (error) {
                    if (error.name === 'AbortError') return;
                    status.textContent = error.message || 'Search is temporarily unavailable.';
                }
            }, 250);
        });
    }

    const stats = document.querySelector('[data-stats-url]');
    if (stats) {
        const updateStats = async () => {
            try {
                const response = await fetch(stats.dataset.statsUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!response.ok) {
                    throw new Error('Live statistics are temporarily unavailable.');
                }
                const payload = await response.json();
                if (!payload.ok) {
                    throw new Error(payload.error || 'Live statistics are temporarily unavailable.');
                }

                const values = {
                    total: payload.medicine.total,
                    low: payload.medicine.low,
                    expired: payload.medicine.expired,
                    units: payload.medicine.units,
                    active: payload.alerts.active,
                };
                Object.entries(values).forEach(([key, value]) => {
                    const element = stats.querySelector(`[data-stat="${key}"]`);
                    if (element) element.textContent = Number(value).toLocaleString();
                });
                const status = stats.querySelector('[data-stats-status]');
                if (status) status.textContent = '';
            } catch (error) {
                const status = stats.querySelector('[data-stats-status]');
                if (status) status.textContent = error.message || 'Live statistics are temporarily unavailable.';
            }
        };
        window.setInterval(updateStats, 60000);
    }
})();
