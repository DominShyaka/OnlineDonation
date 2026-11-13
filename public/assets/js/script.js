document.addEventListener('DOMContentLoaded', () => {
    // Registration form: check that passwords match.
    const registerForm = document.querySelector('form');

    if (registerForm) {
        const password = registerForm.querySelector('input[name="password"]');
        const confirmPassword = registerForm.querySelector(
            'input[name="confirm_password"]'
        );

        if (password && confirmPassword) {
            const checkPasswords = () => {
                if (confirmPassword.value !== password.value) {
                    confirmPassword.setCustomValidity('Passwords do not match.');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            };

            password.addEventListener('input', checkPasswords);
            confirmPassword.addEventListener('input', checkPasswords);

            registerForm.addEventListener('submit', (event) => {
                checkPasswords();

                if (!registerForm.checkValidity()) {
                    event.preventDefault();
                    registerForm.reportValidity();
                }
            });
        }
    }

    // Campaign search: uses the Fetch API when a search field is added.
    const searchInput = document.querySelector('[data-campaign-search]');
    const searchResults = document.querySelector('[data-search-results]');

    if (searchInput && searchResults && searchInput.dataset.searchUrl) {
        let searchTimer;

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);

            const query = searchInput.value.trim();

            if (query.length === 0) {
                searchResults.replaceChildren();
                return;
            }

            searchTimer = setTimeout(async () => {
                searchResults.textContent = 'Searching...';

                try {
                    const url = new URL(
                        searchInput.dataset.searchUrl,
                        window.location.origin
                    );
                    url.searchParams.set('q', query);

                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Search request failed.');
                    }

                    const data = await response.json();
                    searchResults.replaceChildren();

                    if (!data.success || !Array.isArray(data.campaigns)) {
                        searchResults.textContent = 'Unable to load results.';
                        return;
                    }

                    if (data.campaigns.length === 0) {
                        searchResults.textContent = 'No campaigns found.';
                        return;
                    }

                    data.campaigns.forEach((campaign) => {
                        const item = document.createElement('div');
                        item.className = 'search-result';

                        const heading = document.createElement('h3');
                        heading.textContent = campaign.title ?? 'Untitled campaign';

                        const description = document.createElement('p');
                        description.textContent =
                            campaign.description ?? '';

                        item.append(heading, description);
                        searchResults.appendChild(item);
                    });
                } catch (error) {
                    searchResults.textContent =
                        'Search is temporarily unavailable. Please try again.';
                    console.error(error);
                }
            }, 300);
        });
    }
});
