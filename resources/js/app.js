import 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    function isSnowSeason() {
        const now = new Date();
        return (now.getMonth() === 10 && now.getDate() >= 15) || now.getMonth() === 11;
    }

    const themeSelect = document.getElementById('theme-select');
    const themePreferenceKey = 's0ng.theme';
    const availableThemes = ['normal', 'dark', 'christmas'];

    if (themeSelect) {
        const christmasOption = themeSelect.querySelector('option[value="christmas"]');

        function applyTheme(theme) {
            document.body.dataset.theme = theme;
            themeSelect.value = theme;
        }

        function synchronizeThemeSeason() {
            const seasonActive = isSnowSeason();
            const savedTheme = window.localStorage.getItem(themePreferenceKey);
            const selectedTheme = availableThemes.indexOf(savedTheme) === -1
                ? (seasonActive ? 'christmas' : 'normal')
                : savedTheme;

            christmasOption.disabled = !seasonActive;
            christmasOption.textContent = seasonActive ? 'Jul' : 'Jul (15/11-31/12)';
            applyTheme(selectedTheme === 'christmas' && !seasonActive ? 'normal' : selectedTheme);
        }

        synchronizeThemeSeason();

        themeSelect.addEventListener('change', function () {
            const theme = themeSelect.value;

            if (availableThemes.indexOf(theme) === -1 || (theme === 'christmas' && !isSnowSeason())) {
                synchronizeThemeSeason();
                return;
            }

            applyTheme(theme);
            window.localStorage.setItem(themePreferenceKey, theme);
        });

        window.setInterval(synchronizeThemeSeason, 60000);
        document.addEventListener('visibilitychange', synchronizeThemeSeason);
    }

    const snowToggle = document.getElementById('snow-toggle');
    const snowEffect = document.getElementById('snow-effect');
    const snowPreferenceKey = 's0ng.snow.enabled';

    if (snowToggle && snowEffect) {
        const savedPreference = window.localStorage.getItem(snowPreferenceKey);

        let seasonActive = isSnowSeason();
        let snowEnabled = seasonActive && savedPreference !== 'false';

        function renderSnow() {
            snowToggle.disabled = !seasonActive;
            snowToggle.textContent = seasonActive
                ? (snowEnabled ? 'Sne: til' : 'Sne: fra')
                : 'Sne: kun 15/11-31/12';
            snowToggle.setAttribute('aria-pressed', String(snowEnabled));
            snowEffect.replaceChildren();

            if (!seasonActive || !snowEnabled) {
                return;
            }

            for (let index = 0; index < 36; index += 1) {
                const flake = document.createElement('span');
                flake.className = 'snowflake';
                flake.textContent = '❄';
                flake.style.left = Math.random() * 100 + 'vw';
                flake.style.fontSize = 8 + Math.random() * 12 + 'px';
                flake.style.opacity = 0.35 + Math.random() * 0.6;
                flake.style.animationDuration = 8 + Math.random() * 12 + 's';
                flake.style.animationDelay = -Math.random() * 20 + 's';
                flake.style.setProperty('--snow-drift', Math.random() * 100 - 50 + 'px');
                snowEffect.appendChild(flake);
            }
        }

        function synchronizeSeason() {
            const currentSeason = isSnowSeason();

            if (currentSeason === seasonActive) {
                return;
            }

            seasonActive = currentSeason;
            snowEnabled = seasonActive && window.localStorage.getItem(snowPreferenceKey) !== 'false';
            renderSnow();
        }

        snowToggle.addEventListener('click', function () {
            synchronizeSeason();

            if (!seasonActive) {
                return;
            }

            snowEnabled = !snowEnabled;
            window.localStorage.setItem(snowPreferenceKey, String(snowEnabled));
            renderSnow();
        });

        renderSnow();
        window.setInterval(synchronizeSeason, 60000);
        document.addEventListener('visibilitychange', synchronizeSeason);
    }

    const notifications = document.getElementById('song-start-notifications');

    if (!notifications) {
        return;
    }

    let lastId = Number(notifications.dataset.latestId);
    let isPolling = false;

    function showNotification(start) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-info alert-dismissible d-flex align-items-center justify-content-between shadow';
        alert.setAttribute('role', 'status');
        alert.style.pointerEvents = 'auto';

        const message = document.createElement('span');
        message.textContent = 'Der startes nu på: ' + start.song_name + (start.artist_name ? ' - ' + start.artist_name : '');
        alert.appendChild(message);

        if (start.url) {
            const link = document.createElement('a');
            link.className = 'btn btn-sm btn-primary ms-3';
            link.href = start.url;
            link.textContent = 'Åbn sangen';
            alert.appendChild(link);
        }

        const dismiss = document.createElement('button');
        dismiss.className = 'btn-close ms-3';
        dismiss.type = 'button';
        dismiss.setAttribute('aria-label', 'Luk besked');
        dismiss.addEventListener('click', function () {
            alert.remove();
        });
        alert.appendChild(dismiss);

        notifications.appendChild(alert);
    }

    async function checkForSongStarts() {
        if (isPolling) {
            return;
        }

        isPolling = true;

        try {
            const separator = notifications.dataset.pollUrl.indexOf('?') === -1 ? '?' : '&';
            const response = await fetch(
                notifications.dataset.pollUrl + separator + 'after=' + encodeURIComponent(lastId),
                { headers: { Accept: 'application/json' } }
            );

            if (!response.ok) {
                throw new Error('Song-start check failed with status ' + response.status);
            }

            const data = await response.json();

            data.starts.forEach(function (start) {
                showNotification(start);
                lastId = Math.max(lastId, Number(start.id));
            });
        } catch (error) {
            console.error('Could not check for new song starts.', error);
        } finally {
            isPolling = false;
        }
    }

    checkForSongStarts();
    window.setInterval(checkForSongStarts, 3000);
});