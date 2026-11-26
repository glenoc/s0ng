import 'bootstrap';
import QRCode from 'qrcode';
import { addSnow, createSnowPile, createSnowProfile, depositSnow, pushSnow } from './snow-pile.mjs';
import { assessPlayability, extractChords, findCapo, formatCapoLine, transposeChordLine } from './transposition.mjs';
import { guitarDiagramStart, guitarVoicing } from './guitar-chords.mjs';

document.addEventListener('DOMContentLoaded', function () {
    const autoscroll = document.getElementById('song-autoscroll');
    if (autoscroll) {
        const toggle = document.getElementById('autoscroll-toggle');
        const speed = document.getElementById('autoscroll-speed');
        const speedValue = document.getElementById('autoscroll-speed-value');
        const status = document.getElementById('autoscroll-status');
        const wakeStatus = document.getElementById('autoscroll-wake-status');
        const retry = document.getElementById('autoscroll-retry');
        let running = false;
        let frame;
        let previousTime;
        let remainder = 0;
        let wakeLock = null;
        let wakeRequest = 0;
        let saving = false;
        let queuedSpeed = null;

        async function acquireWakeLock(request) {
            if (!('wakeLock' in navigator)) {
                wakeStatus.textContent = autoscroll.dataset.wakeError;
                return;
            }
            try {
                const lock = await navigator.wakeLock.request('screen');
                if (!running || request !== wakeRequest) {
                    await lock.release();
                    return;
                }
                wakeLock = lock;
                lock.addEventListener('release', function () {
                    if (wakeLock === lock) {
                        wakeLock = null;
                        if (running) {
                            wakeStatus.textContent = autoscroll.dataset.wakeError;
                        }
                    }
                });
            } catch (error) {
                console.error('Could not keep the screen awake:', error);
                if (running && request === wakeRequest) {
                    wakeStatus.textContent = autoscroll.dataset.wakeError;
                }
            }
        }

        function stopAutoscroll() {
            running = false;
            window.cancelAnimationFrame(frame);
            toggle.textContent = autoscroll.dataset.start;
            toggle.setAttribute('aria-pressed', 'false');
            wakeStatus.textContent = '';
            wakeRequest += 1;
            if (wakeLock) {
                const lock = wakeLock;
                wakeLock = null;
                lock.release().catch(error => console.error('Could not release the screen wake lock:', error));
            }
        }

        function scrollFrame(time) {
            if (!running) {
                return;
            }
            if (previousTime !== undefined) {
                remainder += Math.min(time - previousTime, 100) * Number(speed.value) / 1000;
                // Avoid losing a pixel to floating-point rounding.
                const pixels = Math.floor(remainder + 1e-7);
                if (pixels > 0) {
                    window.scrollBy({ top: pixels, behavior: 'instant' });
                    remainder = Math.max(0, remainder - pixels);
                }
            }
            previousTime = time;
            if (window.scrollY >= document.documentElement.scrollHeight - window.innerHeight - 1) {
                stopAutoscroll();
            } else {
                frame = window.requestAnimationFrame(scrollFrame);
            }
        }

        toggle.addEventListener('click', function () {
            if (running) {
                stopAutoscroll();
                return;
            }
            running = true;
            previousTime = undefined;
            remainder = 0;
            toggle.textContent = autoscroll.dataset.stop;
            toggle.setAttribute('aria-pressed', 'true');
            wakeStatus.textContent = '';
            acquireWakeLock(++wakeRequest);
            frame = window.requestAnimationFrame(scrollFrame);
        });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden && running) {
                stopAutoscroll();
            }
        });

        async function saveSpeed() {
            queuedSpeed = Number(speed.value);
            if (saving) {
                return;
            }
            saving = true;
            status.textContent = autoscroll.dataset.saving;
            retry.hidden = true;
            while (queuedSpeed !== null) {
                const value = queuedSpeed;
                queuedSpeed = null;
                try {
                    const response = await fetch(autoscroll.dataset.saveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ speed: value }),
                        keepalive: true
                    });
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                } catch (error) {
                    console.error('Could not save autoscroll speed:', error);
                    if (queuedSpeed === null) {
                        status.textContent = autoscroll.dataset.error;
                        retry.hidden = false;
                        saving = false;
                        return;
                    }
                }
            }
            status.textContent = autoscroll.dataset.saved;
            saving = false;
        }

        speed.addEventListener('input', function () {
            speedValue.textContent = speed.value;
        });
        speed.addEventListener('change', saveSpeed);
        retry.addEventListener('click', saveSpeed);
        speedValue.textContent = speed.value;
        autoscroll.hidden = false;
    }

    const shareQrToggle = document.getElementById('share-qr-toggle');
    const shareQrModal = document.getElementById('share-qr-modal');
    if (shareQrToggle && shareQrModal) {
        const canvas = document.getElementById('share-qr-canvas');
        const errorMessage = document.getElementById('share-qr-error');
        shareQrModal.addEventListener('show.bs.modal', function () {
            canvas.hidden = true;
            errorMessage.hidden = true;
            QRCode.toCanvas(canvas, window.location.href, { width: 256, margin: 4 }, function (error) {
                if (error) {
                    console.error('Could not generate the share QR code:', error);
                    errorMessage.textContent = shareQrModal.dataset.error;
                    errorMessage.hidden = false;
                } else {
                    canvas.hidden = false;
                }
            });
        });
        shareQrToggle.hidden = false;
    }

    const notationReviewNav = document.getElementById('notation-review-nav');
    const notationReview = document.getElementById('notation-review');
    if (notationReview) {
        let count = 0;
        notationReview.querySelectorAll('[data-notation]').forEach(function (item) {
            const lines = JSON.parse(item.dataset.notation);
            if (assessPlayability(lines, ['C']).status !== 'unknown') {
                item.remove();
                return;
            }

            count += 1;
            const { chords } = extractChords(lines);
            const reasons = item.querySelector('.notation-review-reasons');
            lines.forEach(function (line) {
                const { unknown } = extractChords([line]);
                if (unknown.length) {
                    const reason = document.createElement('li');
                    const text = document.createElement('span');
                    text.textContent = notationReview.dataset.unknown + ': ' + [...new Set(unknown)].join(', ');
                    const notation = document.createElement('code');
                    notation.className = 'd-block text-break';
                    notation.textContent = line;
                    reason.append(text, notation);
                    reasons.appendChild(reason);
                }
            });
            if (!chords.length) {
                const reason = document.createElement('li');
                reason.textContent = notationReview.dataset.noChords;
                reasons.appendChild(reason);
            }
            if (findCapo(lines).fret > 24) {
                const reason = document.createElement('li');
                reason.textContent = notationReview.dataset.capoError;
                reasons.appendChild(reason);
            }
            item.hidden = false;
        });
        notationReview.querySelector('[role=status]').textContent = count
            ? notationReview.dataset.count.replace(':count', String(count))
            : notationReview.dataset.empty;
        if (notationReviewNav) {
            notationReviewNav.hidden = count === 0;
        }
    } else if (notationReviewNav) {
        fetch(notationReviewNav.dataset.url, { headers: { Accept: 'application/json' }, cache: 'no-store' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(songs => {
                notationReviewNav.hidden = !songs.some(lines => assessPlayability(lines, ['C']).status === 'unknown');
            })
            .catch(error => {
                console.error('Could not check song notation:', error);
                const link = notationReviewNav.querySelector('a');
                link.textContent += ' (' + notationReviewNav.dataset.error + ')';
                notationReviewNav.hidden = false;
            });
    }

    const knownChordsForm = document.getElementById('known-chords-form');
    if (knownChordsForm) {
        const selected = JSON.parse(knownChordsForm.dataset.knownChords);
        const songNotation = JSON.parse(knownChordsForm.dataset.songNotation);
        const roots = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        const common = roots.flatMap(root => [root, root + 'm']);
        const basic = new Set(['A', 'Am', 'B', 'Bm', 'C', 'Cm', 'D', 'Dm', 'E', 'Em', 'F', 'Fm', 'G', 'Gm']);
        const choices = extractChords([selected.join(' '), common.join(' '), ...songNotation.flat()]).chords;
        const options = document.getElementById('known-chords-options');
        const moreOptions = document.getElementById('more-chords-options');
        const selectedCount = document.getElementById('more-chords-selected');
        function updateSelectedCount() {
            const count = moreOptions.querySelectorAll('input:checked').length;
            selectedCount.textContent = count ? '(' + count + ')' : '';
        }
        choices.sort((a, b) => a.localeCompare(b, 'da')).forEach(function (chord) {
            const label = document.createElement('label');
            label.className = 'btn btn-outline-secondary';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'known_chords[]';
            checkbox.value = chord;
            checkbox.checked = selected.includes(chord);
            checkbox.className = 'form-check-input me-1';
            label.append(checkbox, document.createTextNode(chord));
            (basic.has(chord) ? options : moreOptions).appendChild(label);
        });
        moreOptions.addEventListener('change', updateSelectedCount);
        updateSelectedCount();
        document.getElementById('known-chords-fieldset').hidden = false;
    }

    const playabilityConfig = document.getElementById('song-playability-config');
    const knownChords = playabilityConfig ? JSON.parse(playabilityConfig.dataset.chords) : [];
    const songLyrics = document.getElementById('song-lyrics');
    const guestChordToggle = document.getElementById('guest-chords-toggle');

    if (guestChordToggle) {
        const preferenceKey = 's0ng.guest.chords.enabled';
        function updateGuestChords() {
            const enabled = window.localStorage.getItem(preferenceKey) === 'true';
            guestChordToggle.textContent = enabled ? guestChordToggle.dataset.on : guestChordToggle.dataset.off;
            guestChordToggle.setAttribute('aria-pressed', String(enabled));
            document.querySelectorAll('.guest-chord-line').forEach(line => {
                line.hidden = !enabled;
            });
            document.dispatchEvent(new Event('guest-chords-change'));
        }
        guestChordToggle.addEventListener('click', function () {
            const enabled = guestChordToggle.getAttribute('aria-pressed') !== 'true';
            window.localStorage.setItem(preferenceKey, String(enabled));
            updateGuestChords();
        });
        window.addEventListener('storage', function (event) {
            if (event.key === preferenceKey || event.key === null) {
                updateGuestChords();
            }
        });
        updateGuestChords();
        guestChordToggle.hidden = false;
    }

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

        const transposition = document.getElementById('song-transposition');
        if (transposition) {
            const originals = Array.from(lines, line => line.textContent);
            const originalCapo = findCapo(originals);
            const semitones = document.getElementById('song-semitones');
            const capo = document.getElementById('song-capo');
            const addedCapo = document.createElement('span');
            let selectedChord = null;
            addedCapo.className = 'text-line song-text';
            semitones.value = transposition.dataset.semitones;
            capo.value = transposition.dataset.capo === '' ? String(originalCapo.fret) : transposition.dataset.capo;

            function renderTransposition() {
                const fret = Number(capo.value);
                const shift = Number(semitones.value) + originalCapo.fret - fret;
                document.getElementById('song-semitones-value').textContent = Number(semitones.value) > 0 ? '+' + semitones.value : semitones.value;
                document.getElementById('song-capo-value').textContent = capo.value;
                transposition.querySelectorAll('[data-adjust]').forEach(function (button) {
                    const value = Number(button.dataset.adjust === 'semitones' ? semitones.value : capo.value);
                    const min = button.dataset.adjust === 'semitones' ? -12 : 0;
                    const max = button.dataset.adjust === 'semitones' ? 12 : 24;
                    button.disabled = Number(button.dataset.step) < 0 ? value <= min : value >= max;
                });
                lines.forEach(function (line, index) {
                    if (index === originalCapo.index) {
                        line.textContent = fret === originalCapo.fret ? originals[index] : formatCapoLine(originals[index], fret);
                    } else {
                        line.textContent = line.classList.contains('chord-line')
                            ? transposeChordLine(originals[index], shift)
                            : originals[index];
                    }
                });
                addedCapo.textContent = originalCapo.index === -1 && fret !== 0 ? 'Capo: ' + fret : '';
                addedCapo.hidden = !addedCapo.textContent;
                const summary = document.getElementById('song-transposition-summary');
                const signed = value => value > 0 ? '+' + value : String(value);
                summary.textContent = summary.dataset.summary.replace(':chords', signed(shift)).replace(':total', signed(Number(semitones.value)));
                renderChordOverview();
                fitSongLyrics();
            }

            function renderChordOverview() {
                const overview = document.getElementById('song-chord-overview');
                const chordLines = Array.from(lines).filter(line => line.classList.contains('chord-line')).map(line => line.textContent);
                const result = extractChords(chordLines);
                const list = overview.querySelector('span');
                list.replaceChildren();
                if (!result.chords.length) {
                    list.append(document.createTextNode(overview.dataset.empty));
                }
                if (!result.chords.includes(selectedChord)) {
                    selectedChord = null;
                }
                result.chords.forEach(function (chord) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-sm btn-outline-secondary me-1 mb-1';
                    button.textContent = chord;
                    button.setAttribute('aria-controls', 'song-chord-diagram');
                    button.setAttribute('aria-expanded', String(chord === selectedChord));
                    button.addEventListener('click', function () {
                        selectedChord = selectedChord === chord ? null : chord;
                        list.querySelectorAll('button').forEach(item => item.setAttribute('aria-expanded', String(item.textContent === selectedChord)));
                        renderChordDiagram();
                    });
                    list.appendChild(button);
                });
                if (result.unknown.length) {
                    list.append(document.createTextNode(' ' + overview.dataset.unknown));
                }
                renderChordDiagram();
            }

            function renderChordDiagram() {
                const diagram = document.getElementById('song-chord-diagram');
                diagram.replaceChildren();
                diagram.hidden = selectedChord === null;
                if (selectedChord === null) {
                    return;
                }
                const title = document.createElement('strong');
                title.textContent = diagram.dataset.title.replace(':chord', selectedChord);
                diagram.appendChild(title);
                const frets = guitarVoicing(selectedChord);
                if (!frets) {
                    const message = document.createElement('p');
                    message.textContent = diagram.dataset.unavailable;
                    diagram.appendChild(message);
                    return;
                }

                const base = guitarDiagramStart(frets);
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 150 140');
                svg.setAttribute('width', '150');
                svg.setAttribute('height', '140');
                svg.setAttribute('role', 'img');
                svg.setAttribute('aria-label', diagram.dataset.description.replace(':chord', selectedChord).replace(':frets', frets.map(fret => fret < 0 ? 'X' : fret === 0 ? diagram.dataset.open : fret).join(', ')));
                svg.style.color = 'inherit';
                function element(name, attributes) {
                    const node = document.createElementNS('http://www.w3.org/2000/svg', name);
                    Object.entries(attributes).forEach(([key, value]) => node.setAttribute(key, value));
                    svg.appendChild(node);
                    return node;
                }
                for (let string = 0; string < 6; string += 1) {
                    const x = 25 + string * 20;
                    element('line', { x1: x, y1: 30, x2: x, y2: 126, stroke: 'currentColor' });
                    if (frets[string] < 0) {
                        element('text', { x, y: 22, 'text-anchor': 'middle', fill: 'currentColor' }).textContent = 'X';
                    } else if (frets[string] > 0) {
                        element('circle', { cx: x, cy: 42 + (frets[string] - base) * 24, r: 6, fill: 'currentColor' });
                    }
                }
                for (let fret = 0; fret <= 4; fret += 1) {
                    element('line', { x1: 25, y1: 30 + fret * 24, x2: 125, y2: 30 + fret * 24, stroke: 'currentColor', 'stroke-width': fret === 0 && base === 1 ? 3 : 1 });
                }
                diagram.appendChild(svg);
                const help = document.createElement('div');
                help.textContent = diagram.dataset.fret.replace(':fret', String(base)) + '. ' + diagram.dataset.help;
                diagram.appendChild(help);
            }

            transposition.querySelectorAll('[data-adjust]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = button.dataset.adjust === 'semitones' ? semitones : capo;
                    input.value = String(Number(input.value) + Number(button.dataset.step));
                    renderTransposition();
                });
            });
            document.getElementById('song-transposition-reset').addEventListener('click', function () {
                semitones.value = '0';
                capo.value = String(originalCapo.fret);
                renderTransposition();
            });
            if (originalCapo.fret > 24) {
                const error = document.createElement('p');
                error.className = 'text-danger';
                error.textContent = transposition.dataset.capoError;
                transposition.appendChild(error);
                renderChordOverview();
            } else {
                songLyrics.prepend(addedCapo);
                transposition.querySelector('fieldset').hidden = false;
                renderTransposition();
            }
        }

        fitSongLyrics();
        document.addEventListener('guest-chords-change', fitSongLyrics);
        window.addEventListener('resize', fitSongLyrics);
        document.fonts.ready.then(fitSongLyrics);
    }

    if (knownChords.length > 0) {
        document.querySelectorAll('.song-playability').forEach(function (badge) {
            const result = assessPlayability(JSON.parse(badge.dataset.notation), knownChords, {
                semitones: Number(badge.dataset.semitones),
                capo: badge.dataset.capo === '' ? undefined : Number(badge.dataset.capo)
            });
            const labels = {
                'no-chords': ['noChords', 'text-bg-secondary'],
                playable: ['playable', 'text-bg-success'],
                capo: ['withCapo', 'text-bg-info'],
                unplayable: ['unplayable', 'text-bg-warning'],
                unknown: ['unknown', 'text-bg-secondary']
            };
            const [label, colour] = labels[result.status];
            badge.textContent = playabilityConfig.dataset[label].replace(':fret', String(result.fret));
            badge.classList.add(colour);
            badge.hidden = false;
        });
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
    const snowGame = document.getElementById('snow-game');
    const snowPreferenceKey = 's0ng.snow.enabled';
    const snowProgressKey = 's0ng.snow.cleared.v2';

    if (snowToggle && snowEffect && snowShovel && snowGame) {
        const savedPreference = window.localStorage.getItem(snowPreferenceKey);
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        const pileHeight = 60;
        const tools = [
            { name: 'small', threshold: 0, capacity: 90, width: 42, height: 60 },
            { name: 'large', threshold: 1500, capacity: 180, width: 56, height: 72 },
            { name: 'scraper', threshold: 5000, capacity: 360, width: 88, height: 70 },
            { name: 'plow', threshold: 13000, capacity: 720, width: 148, height: 88 },
            { name: 'blower', threshold: 30000, capacity: 1440, width: 168, height: 96 }
        ];
        let progressValue = window.localStorage.getItem(snowProgressKey);
        if (progressValue === null) {
            const previousProgress = Number(window.localStorage.getItem('s0ng.snow.cleared'));
            if (Number.isFinite(previousProgress) && previousProgress > 0 && Number.isFinite(previousProgress * 10)) {
                progressValue = String(previousProgress * 10);
                window.localStorage.setItem(snowProgressKey, progressValue);
            }
        }
        const savedProgress = Number(progressValue);
        let clearedSnow = Number.isFinite(savedProgress) && savedProgress > 0 ? savedProgress : 0;
        let toolIndex = 0;

        let seasonActive = isSnowSeason();
        let snowEnabled = seasonActive && (!canToggleSnow() || savedPreference !== 'false');
        let pile;
        let pileProfile;
        let canvas;
        let snowTimer;
        let pointerId = null;
        let lastIndex = null;
        let gripOffset = 0;
        let keyboardIndex = 0;
        let shovelLoad = 0;
        let pushDirection = 1;
        let blowerTimer;

        function canToggleSnow() {
            return snowGame.dataset.authenticated === 'true' || clearedSnow >= tools[tools.length - 1].threshold;
        }

        function updateSnowToggle() {
            snowToggle.hidden = !canToggleSnow();
            snowToggle.disabled = !seasonActive;
            snowToggle.textContent = seasonActive
                ? (snowEnabled ? snowToggle.dataset.snowOn : snowToggle.dataset.snowOff)
                : snowToggle.dataset.snowSeason;
            snowToggle.setAttribute('aria-pressed', String(snowEnabled));
        }

        function updateSnowGame(announce = false) {
            const previousTool = toolIndex;
            toolIndex = tools.findIndex((tool, index) => !tools[index + 1] || clearedSnow < tools[index + 1].threshold);
            const tool = tools[toolIndex];
            const next = tools[toolIndex + 1];
            const name = snowGame.dataset[tool.name];
            snowGame.querySelector('#snow-game-tool').textContent = name;
            snowShovel.setAttribute('aria-label', name);
            snowShovel.style.setProperty('--tool-width', tool.width + 'px');
            snowShovel.style.setProperty('--tool-height', tool.height + 'px');
            snowShovel.querySelectorAll('[data-tool]').forEach(icon => {
                icon.toggleAttribute('hidden', icon.dataset.tool !== tool.name);
            });
            const progress = snowGame.querySelector('#snow-game-progress');
            const meter = snowGame.querySelector('#snow-game-meter');
            meter.hidden = !next;
            snowGame.hidden = snowShovel.hidden || !next;
            updateSnowToggle();
            if (next) {
                progress.textContent = snowGame.dataset.next
                    .replace(':tool', snowGame.dataset[next.name])
                    .replace(':current', String(Math.floor(clearedSnow - tool.threshold)))
                    .replace(':target', String(next.threshold - tool.threshold));
                meter.max = next.threshold - tool.threshold;
                meter.value = clearedSnow - tool.threshold;
            }
            if (announce && previousTool !== toolIndex) {
                document.getElementById('snow-game-announcement').textContent = snowGame.dataset.unlocked.replace(':tool', name);
                if (next) {
                    snowGame.querySelector('#snow-game-details').open = true;
                }
            }
            if (pile && previousTool !== toolIndex) {
                positionShovel(keyboardIndex);
            }
        }

        function moveSnow(from, to) {
            const tool = tools[toolIndex];
            const result = pushSnow(pile, from, to, tool.capacity, shovelLoad, tool.name === 'blower');
            shovelLoad = result.load;
            if (from !== to && tool.name === 'blower') {
                if (result.cleared > 0) {
                    const cells = Math.abs(to - from) + (to === 0 || to === pile.length - 1 ? 1 : 0);
                    const density = result.cleared / cells;
                    snowShovel.dataset.snowBlow = density < 10 ? 'light' : density < 30 ? 'medium' : 'heavy';
                    window.clearTimeout(blowerTimer);
                    blowerTimer = window.setTimeout(stopBlowing, 250);
                } else {
                    stopBlowing();
                }
            }
            if (result.cleared > 0) {
                clearedSnow += result.cleared;
                window.localStorage.setItem(snowProgressKey, String(clearedSnow));
                updateSnowGame(true);
            }
        }

        function stopBlowing() {
            window.clearTimeout(blowerTimer);
            delete snowShovel.dataset.snowBlow;
        }

        updateSnowGame();

        window.addEventListener('storage', function (event) {
            if (event.key === snowProgressKey && event.newValue !== null) {
                const progress = Number(event.newValue);
                if (Number.isFinite(progress) && progress > clearedSnow) {
                    clearedSnow = progress;
                    updateSnowGame(true);
                }
            }
        });

        const visualViewport = window.visualViewport;

        if (visualViewport) {
            function positionSnowAtViewportBottom() {
                const bottom = window.innerHeight - visualViewport.height - visualViewport.offsetTop;
                snowEffect.style.bottom = bottom + 'px';
                snowShovel.style.bottom = bottom + 'px';
                snowGame.style.bottom = (bottom + 100) + 'px';
                if (canvas) {
                    canvas.style.bottom = bottom + 'px';
                }
            }

            positionSnowAtViewportBottom();
            visualViewport.addEventListener('resize', positionSnowAtViewportBottom);
            visualViewport.addEventListener('scroll', positionSnowAtViewportBottom);
            window.addEventListener('resize', positionSnowAtViewportBottom);
        }

        function settleSnow() {
            if (!pile || shovelLoad === 0) {
                return;
            }
            shovelLoad = depositSnow(pile, moundIndex(), shovelLoad, pileHeight);
            if (shovelLoad > 0) {
                throw new Error('Could not deposit all snow from the shovel.');
            }
            drawPile();
        }

        function positionShovel(index) {
            keyboardIndex = index;
            const halfWidth = tools[toolIndex].width / 2;
            const center = (index + 0.5) / pile.length * window.innerWidth;
            snowShovel.style.left = Math.max(halfWidth, Math.min(window.innerWidth - halfWidth, center)) + 'px';
        }

        function tiltShovel(direction) {
            if (direction) {
                pushDirection = direction;
                snowShovel.style.setProperty('--tool-direction', direction);
            }
            snowShovel.style.setProperty('--shovel-tilt', (-12 * direction) + 'deg');
        }

        function moundIndex() {
            const step = canvas.clientWidth / pile.length;
            const center = parseFloat(snowShovel.style.left);
            const front = center + pushDirection * (tools[toolIndex].width / 2 + 2 * step);
            return Math.max(0, Math.min(pile.length - 1, Math.round(front / step - 0.5)));
        }

        function drawPile() {
            const context = canvas.getContext('2d');
            const width = canvas.clientWidth;
            const height = canvas.clientHeight;
            const step = width / pile.length;
            const moundWeights = [1, 2, 3, 2, 1];
            const mound = moundIndex();
            const heights = Array.from(pile, function (value, index) {
                const weight = moundWeights[index - mound + 2] || 0;
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
            const halfWidth = tools[toolIndex].width / 2;
            if (clientX <= halfWidth) {
                return 0;
            }
            if (clientX >= window.innerWidth - halfWidth) {
                return pile.length - 1;
            }
            return Math.max(0, Math.min(pile.length - 1, Math.floor(clientX / window.innerWidth * pile.length)));
        }

        function startPile() {
            canvas = document.createElement('canvas');
            canvas.className = 'snow-pile';
            canvas.setAttribute('aria-hidden', 'true');
            canvas.style.bottom = snowShovel.style.bottom;
            snowEffect.after(canvas);
            resizePile();
            positionShovel(Math.floor(pile.length * 0.96));
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
            updateSnowToggle();
            window.clearInterval(snowTimer);
            window.removeEventListener('resize', resizePile);
            if (pointerId !== null && snowShovel.hasPointerCapture(pointerId)) {
                snowShovel.releasePointerCapture(pointerId);
            }
            pointerId = null;
            lastIndex = null;
            shovelLoad = 0;
            stopBlowing();
            tiltShovel(0);
            snowShovel.hidden = !seasonActive || !snowEnabled || reducedMotion.matches;
            snowGame.hidden = snowShovel.hidden || toolIndex === tools.length - 1;
            snowEffect.replaceChildren();
            if (canvas) {
                canvas.remove();
            }
            canvas = null;
            pile = null;
            pileProfile = null;

            if (!seasonActive || !snowEnabled || reducedMotion.matches) {
                return;
            }
            let snowflakeCount = (window.innerWidth + window.innerHeight) / 50;
            for (let index = 0; index < snowflakeCount; index += 1) {
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
            snowEnabled = seasonActive && (!canToggleSnow() || window.localStorage.getItem(snowPreferenceKey) !== 'false');
            renderSnow();
        }

        snowToggle.addEventListener('click', function () {
            synchronizeSeason();

            if (!seasonActive || !canToggleSnow()) {
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
            moveSnow(lastIndex, index);
            lastIndex = index;
            positionShovel(index);
            drawPile();
        });
        function endDrag(event) {
            if (event.pointerId === pointerId) {
                pointerId = null;
                lastIndex = null;
                stopBlowing();
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
            moveSnow(keyboardIndex, next);
            positionShovel(next);
            drawPile();
        });
        snowShovel.addEventListener('keyup', function (event) {
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                stopBlowing();
                tiltShovel(0);
                settleSnow();
            }
        });
        snowShovel.addEventListener('blur', function () {
            stopBlowing();
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
