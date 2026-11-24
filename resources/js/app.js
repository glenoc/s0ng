import 'bootstrap';
import { addSnow, createSnowPile, createSnowProfile, depositSnow, pushSnow } from './snow-pile.mjs';

document.addEventListener('DOMContentLoaded', function () {
    const songLyrics = document.getElementById('song-lyrics');

    if (songLyrics) {
        const mobileScreen = window.matchMedia('(max-width: 600px)');
        const lines = songLyrics.querySelectorAll('.song-text');

        function fitSongLyrics() {
            songLyrics.style.fontSize = '';

            if (!mobileScreen.matches || !songLyrics.clientWidth) {
                return;
            }

            const fontSize = parseFloat(window.getComputedStyle(songLyrics).fontSize);
            let widestLine = songLyrics.clientWidth;

            lines.forEach(function (line) {
                widestLine = Math.max(widestLine, line.scrollWidth);
            });

            if (widestLine > songLyrics.clientWidth) {
                songLyrics.style.fontSize = (fontSize * songLyrics.clientWidth / (widestLine + 1)) + 'px';
            }
        }

        fitSongLyrics();
        window.addEventListener('resize', fitSongLyrics);
        document.fonts.ready.then(fitSongLyrics);
    }

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
            christmasOption.textContent = seasonActive ? themeSelect.dataset.christmas : themeSelect.dataset.christmasSeason;
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
    const snowShovel = document.getElementById('snow-shovel');
    const snowEffect = document.getElementById('snow-effect');
    const snowPreferenceKey = 's0ng.snow.enabled';

    if (snowToggle && snowEffect && snowShovel) {
        const savedPreference = window.localStorage.getItem(snowPreferenceKey);
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        const pileHeight = 60;
        const shovelCapacity = 180;

        let seasonActive = isSnowSeason();
        let snowEnabled = seasonActive && savedPreference !== 'false';
        let pile;
        let pileProfile;
        let canvas;
        let snowTimer;
        let pointerId = null;
        let lastIndex = null;
        let gripOffset = 0;
        let keyboardIndex = 0;
        let shovelLoad = 0;

        function settleSnow() {
            if (!pile || shovelLoad === 0) {
                return;
            }
            shovelLoad = depositSnow(pile, keyboardIndex, shovelLoad, pileHeight);
            if (shovelLoad > 0) {
                throw new Error('Could not deposit all snow from the shovel.');
            }
            drawPile();
        }

        function positionShovel(index) {
            keyboardIndex = index;
            snowShovel.style.left = ((index + 0.5) / pile.length * 100) + '%';
        }

        function tiltShovel(direction) {
            snowShovel.style.setProperty('--shovel-tilt', (-12 * direction) + 'deg');
        }

        function drawPile() {
            const context = canvas.getContext('2d');
            const width = canvas.clientWidth;
            const height = canvas.clientHeight;
            const step = width / pile.length;
            const moundWeights = [1, 2, 3, 2, 1];
            const heights = Array.from(pile, function (value, index) {
                const weight = moundWeights[index - keyboardIndex + 2] || 0;
                const groundHeight = value * (1 - pileProfile[index] / pileHeight);
                return Math.min(height - 5, groundHeight + shovelLoad * weight / 9);
            });
            const surface = heights.map(function (value, index) {
                const left = heights[Math.max(0, index - 1)];
                const right = heights[Math.min(heights.length - 1, index + 1)];
                return (left + 2 * value + right) / 4;
            });

            context.clearRect(0, 0, width, height);
            context.fillStyle = 'rgba(245, 251, 255, 0.94)';
            context.beginPath();
            context.moveTo(0, height);
            context.lineTo(0, height - surface[0]);
            for (let index = 0; index < pile.length; index += 1) {
                const next = surface[Math.min(index + 1, pile.length - 1)];
                context.quadraticCurveTo(
                    (index + 0.5) * step,
                    height - surface[index],
                    (index + 1) * step,
                    height - (surface[index] + next) / 2
                );
            }
            context.lineTo(width, height);
            context.closePath();
            context.fill();
        }

        function resizePile() {
            settleSnow();
            const previous = pile;
            pile = createSnowPile(window.innerWidth);
            pileProfile = createSnowProfile(pile.length);
            if (previous) {
                for (let index = 0; index < pile.length; index += 1) {
                    pile[index] = previous[Math.min(previous.length - 1, Math.floor(index * previous.length / pile.length))];
                }
            }
            const ratio = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.round(canvas.clientWidth * ratio);
            canvas.height = Math.round(canvas.clientHeight * ratio);
            canvas.getContext('2d').setTransform(ratio, 0, 0, ratio, 0, 0);
            positionShovel(Math.min(pile.length - 1, Math.floor((keyboardIndex + 0.5) * pile.length / (previous ? previous.length : pile.length))));
            if (pointerId !== null) {
                lastIndex = keyboardIndex;
            }
            drawPile();
        }

        function snowIndex(clientX) {
            if (clientX <= 26) {
                return 0;
            }
            if (clientX >= window.innerWidth - 26) {
                return pile.length - 1;
            }
            return Math.max(0, Math.min(pile.length - 1, Math.floor(clientX / window.innerWidth * pile.length)));
        }

        function startPile() {
            canvas = document.createElement('canvas');
            canvas.className = 'snow-pile';
            snowEffect.appendChild(canvas);
            resizePile();
            positionShovel(Math.floor(pile.length * 0.96));
            snowShovel.style.left = '96%';
            window.addEventListener('resize', resizePile);
            snowTimer = window.setInterval(function () {
                let freeSpace = pile.reduce((total, height) => total + pileHeight - height, 0) - shovelLoad;
                for (let index = 0; index < 4; index += 1) {
                    const position = Math.floor(Math.random() * pile.length);
                    const before = pile[position];
                    addSnow(pile, position, Math.min(2, Math.max(0, freeSpace)), pileHeight);
                    freeSpace -= pile[position] - before;
                }
                drawPile();
            }, 250);
        }

        function renderSnow() {
            snowToggle.disabled = !seasonActive;
            snowToggle.textContent = seasonActive
                ? (snowEnabled ? snowToggle.dataset.snowOn : snowToggle.dataset.snowOff)
                : snowToggle.dataset.snowSeason;
            snowToggle.setAttribute('aria-pressed', String(snowEnabled));
            window.clearInterval(snowTimer);
            window.removeEventListener('resize', resizePile);
            if (pointerId !== null && snowShovel.hasPointerCapture(pointerId)) {
                snowShovel.releasePointerCapture(pointerId);
            }
            pointerId = null;
            lastIndex = null;
            shovelLoad = 0;
            tiltShovel(0);
            snowShovel.hidden = !seasonActive || !snowEnabled || reducedMotion.matches;
            snowEffect.replaceChildren();
            canvas = null;
            pile = null;
            pileProfile = null;

            if (!seasonActive || !snowEnabled || reducedMotion.matches) {
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
            startPile();
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

        snowShovel.addEventListener('pointerdown', function (event) {
            if (!pile || pointerId !== null) {
                return;
            }
            pointerId = event.pointerId;
            lastIndex = keyboardIndex;
            const shovelBounds = snowShovel.getBoundingClientRect();
            gripOffset = event.clientX - (shovelBounds.left + shovelBounds.width / 2);
            snowShovel.setPointerCapture(pointerId);
        });
        snowShovel.addEventListener('pointermove', function (event) {
            if (event.pointerId !== pointerId || !pile) {
                return;
            }
            const index = snowIndex(event.clientX - gripOffset);
            if (index !== lastIndex) {
                tiltShovel(Math.sign(index - lastIndex));
            }
            shovelLoad = pushSnow(pile, lastIndex, index, shovelCapacity, shovelLoad);
            lastIndex = index;
            positionShovel(index);
            drawPile();
        });
        function endDrag(event) {
            if (event.pointerId === pointerId) {
                pointerId = null;
                lastIndex = null;
                tiltShovel(0);
                settleSnow();
            }
        }
        snowShovel.addEventListener('pointerup', endDrag);
        snowShovel.addEventListener('pointercancel', endDrag);
        snowShovel.addEventListener('lostpointercapture', endDrag);
        snowShovel.addEventListener('keydown', function (event) {
            if (!pile || (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight')) {
                return;
            }
            event.preventDefault();
            const direction = event.key === 'ArrowLeft' ? -1 : 1;
            const next = Math.max(0, Math.min(pile.length - 1, keyboardIndex + direction));
            if (next !== keyboardIndex) {
                tiltShovel(direction);
            }
            shovelLoad = pushSnow(pile, keyboardIndex, next, shovelCapacity, shovelLoad);
            positionShovel(next);
            drawPile();
        });
        snowShovel.addEventListener('keyup', function (event) {
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                tiltShovel(0);
                settleSnow();
            }
        });
        snowShovel.addEventListener('blur', function () {
            tiltShovel(0);
            settleSnow();
        });
        reducedMotion.addEventListener('change', renderSnow);

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
        message.textContent = notifications.dataset.songStarted.replace(':song', () => start.song_name) + (start.artist_name ? ' - ' + start.artist_name : '');
        alert.appendChild(message);

        if (start.url) {
            const link = document.createElement('a');
            link.className = 'btn btn-sm btn-primary ms-3';
            link.href = start.url;
            link.textContent = notifications.dataset.openSong;
            alert.appendChild(link);
        }

        const dismiss = document.createElement('button');
        dismiss.className = 'btn-close ms-3';
        dismiss.type = 'button';
        dismiss.setAttribute('aria-label', notifications.dataset.closeNotification);
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