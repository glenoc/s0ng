window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js');
require('bootstrap');

document.addEventListener('DOMContentLoaded', function () {
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