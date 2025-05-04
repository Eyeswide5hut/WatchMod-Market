/**
 * Watch Builder functionality for WatchModMarket
 */
(function ($) {
    "use strict";

    // Variables
    let currentView = "front";
    let selectedParts = {
        "case": "",
        "dial": "",
        "hands": "",
        "strap": "",
        "movement": ""
    };
    let compatibilityWarnings = [];
    let canvas, ctx, renderer, scene, camera, controls;
    let currentModel;
    let isLoading = true;
    let animationFrameId = null;

    // Initialize the builder interface
    function initBuilder() {
        setupTabs();
        setupPartSelection();
        setupViewControls();

        // Initialize either 3D or 2D rendering based on browser capabilities
        if (window.THREE) {
            initThreeJS();
        } else {
            initCanvas();
            createWatchModel2D();
        }

        updatePricing();

        // Hide loading indicator after initialization
        setTimeout(function () {
            isLoading = false;
            $("#loading-indicator").fadeOut();
        }, 1000);

        // Set initial compatibility check
        checkSelectedPartsOnLoad();
    }

    // Check for pre-selected parts on page load
    function checkSelectedPartsOnLoad() {
        // Get initially selected parts
        $('.part-item.selected').each(function () {
            const partId = $(this).data('part-id');
            if (partId) {
                const partType = partId.split('-')[0];
                selectedParts[partType] = partId;
            }
        });

        // Run compatibility check
        checkCompatibility();

        // Update the watch model
        if (window.THREE) {
            updateWatchModel();
        } else {
            createWatchModel2D();
        }
    }

    // Setup tabs in the parts panel
    function setupTabs() {
        $('.parts-tab').on('click', function () {
            const tabId = $(this).attr('id');
            const targetSection = $('#' + tabId.replace('tab-', 'section-'));

            // Update tab state
            $('.parts-tab').removeClass('active').attr('aria-selected', 'false');
            $(this).addClass('active').attr('aria-selected', 'true');

            // Update section visibility
            $('.part-section').removeClass('active').attr('hidden', 'true');
            targetSection.addClass('active').removeAttr('hidden');
        });
    }

    // Setup part selection
    function setupPartSelection() {
        $('.part-item').on('click', function () {
            const $this = $(this);
            const partId = $this.data('part-id');

            if (!partId) return;

            const partType = partId.split('-')[0]; // e.g., "case", "dial", etc.

            // Update selection in UI
            $this.siblings('.part-item').removeClass('selected');
            $this.addClass('selected');

            // Store selected part
            selectedParts[partType] = partId;

            // Check compatibility
            checkCompatibility();

            // Update 3D model
            if (window.THREE) {
                updateWatchModel();
            } else {
                createWatchModel2D();
            }

            // Update pricing
            updatePricing();
        });
    }

    // Setup view controls
    function setupViewControls() {
        $('.view-control').on('click', function () {
            const $this = $(this);
            const view = $this.data('view');

            // Update control state
            $('.view-control').removeClass('active');
            $this.addClass('active');

            // Update current view
            currentView = view;

            // Update view angle
            updateViewAngle();

            // If 2D rendering, update the canvas
            if (!window.THREE) {
                createWatchModel2D();
            }
        });
    }

    // Initialize canvas for 2D fallback
    function initCanvas() {
        canvas = document.getElementById('watch-3d-render');
        if (!canvas) {
            console.error('Canvas element not found');
            return;
        }

        ctx = canvas.getContext('2d');

        // Ensure the canvas is visible
        $(canvas).css('display', 'block');
    }

    // Initialize Three.js for 3D rendering
    function initThreeJS() {
        try {
            // Create scene
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xEEEEEE);

            // Get canvas
            canvas = document.getElementById('watch-3d-render');
            if (!canvas) {
                console.error('Canvas element not found');
                return;
            }

            // Create camera
            camera = new THREE.PerspectiveCamera(45, canvas.width / canvas.height, 0.1, 1000);
            camera.position.set(0, 0, 10);

            // Create renderer
            renderer = new THREE.WebGLRenderer({
                canvas: canvas,
                antialias: true,
                alpha: true
            });
            renderer.setSize(canvas.width, canvas.height);
            renderer.shadowMap.enabled = true;

            // Add lighting
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(1, 1, 1);
            directionalLight.castShadow = true;
            scene.add(directionalLight);

            // Add orbit controls if available
            if (typeof THREE.OrbitControls !== 'undefined') {
                controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.enableDamping = true;
                controls.dampingFactor = 0.05;
                controls.rotateSpeed = 0.7;
                controls.enableZoom = true;
                controls.enablePan = false;
                controls.autoRotate = (currentView === '3d');
                controls.autoRotateSpeed = 1.0;
            }

            // Start animation loop
            animate();

            // Create initial model
            updateWatchModel();
        } catch (error) {
            console.error('Error initializing Three.js:', error);
            // Fall back to 2D canvas
            initCanvas();
            createWatchModel2D();
        }
    }

    // Animation loop for Three.js
    function animate() {
        animationFrameId = requestAnimationFrame(animate);

        if (controls) {
            controls.update();
        }

        if (renderer && scene && camera) {
            renderer.render(scene, camera);
        }
    }

    // Update the watch model based on selected parts
    function updateWatchModel() {
        if (isLoading) return;

        // Show loading indicator
        $('#loading-indicator').fadeIn();

        // Clear previous model
        if (scene && currentModel) {
            scene.remove(currentModel);
        }

        // Create new watch model based on selected parts
        createWatchModel3D();

        // Update view angle
        updateViewAngle();

        // Hide loading indicator
        setTimeout(function () {
            $('#loading-indicator').fadeOut();
        }, 500);
    }

    // Create a 3D watch model using Three.js
    function createWatchModel3D() {
        // Create new group for the watch model
        currentModel = new THREE.Group();

        // Handle each part type
        const partTypes = Object.keys(selectedParts);

        // Check if any parts are selected
        let hasSelectedParts = false;

        partTypes.forEach(partType => {
            if (selectedParts[partType]) {
                const partId = selectedParts[partType].split('-')[1];
                hasSelectedParts = true;
                loadWatchPart(partType, partId);
            } else {
                // Create placeholder for missing part
                createPlaceholderPart(partType);
            }
        });

        // If no parts selected, create a basic watch placeholder
        if (!hasSelectedParts) {
            createBasicWatchPlaceholder();
        }

        // Add the combined model to the scene
        scene.add(currentModel);
    }

    // Create a basic watch placeholder when no parts are selected
    function createBasicWatchPlaceholder() {
        // Case
        const caseGeometry = new THREE.CylinderGeometry(4, 4, 1, 32);
        const caseMaterial = new THREE.MeshStandardMaterial({ color: 0x888888 });
        const caseMesh = new THREE.Mesh(caseGeometry, caseMaterial);
        caseMesh.rotation.x = Math.PI * 0.5;
        currentModel.add(caseMesh);

        // Dial
        const dialGeometry = new THREE.CircleGeometry(3.5, 32);
        const dialMaterial = new THREE.MeshStandardMaterial({ color: 0xFFFFFF });
        const dialMesh = new THREE.Mesh(dialGeometry, dialMaterial);
        dialMesh.position.z = 0.1;
        currentModel.add(dialMesh);

        // Hands
        const hourHandGeo = new THREE.BoxGeometry(0.2, 2, 0.1);
        const minuteHandGeo = new THREE.BoxGeometry(0.2, 3, 0.1);
        const handMaterial = new THREE.MeshStandardMaterial({ color: 0x000000 });

        const hourHand = new THREE.Mesh(hourHandGeo, handMaterial);
        hourHand.position.y = 1;

        const minuteHand = new THREE.Mesh(minuteHandGeo, handMaterial);
        minuteHand.position.y = 1.5;
        minuteHand.rotation.z = Math.PI * 0.25;

        const handsGroup = new THREE.Group();
        handsGroup.add(hourHand);
        handsGroup.add(minuteHand);
        handsGroup.position.z = 0.2;

        currentModel.add(handsGroup);

        // Strap
        const strapGeometry = new THREE.BoxGeometry(2, 8, 0.5);
        const strapMaterial = new THREE.MeshStandardMaterial({ color: 0x000000 });
        const strapMesh = new THREE.Mesh(strapGeometry, strapMaterial);
        strapMesh.position.y = -2;

        currentModel.add(strapMesh);
    }

    // Load individual watch part model
    function loadWatchPart(partType, partId) {
        // Check if THREE.GLTFLoader is available
        if (typeof THREE.GLTFLoader === 'undefined') {
            console.error('THREE.GLTFLoader is not available');
            createPlaceholderPart(partType);
            return;
        }

        // Get model path from wpData global or use a default path
        const basePath = window.wpData && window.wpData.themeUrl
            ? window.wpData.themeUrl + '/assets/models/'
            : '/wp-content/themes/watchmodmarket/assets/models/';

        // Create path to the 3D model
        const modelPath = basePath + partType + '/' + partId + '.gltf';

        // Load the model using GLTFLoader
        const loader = new THREE.GLTFLoader();
        loader.load(
            modelPath,
            function (gltf) {
                const model = gltf.scene;

                // Position the part correctly based on type
                positionWatchPart(partType, model);

                // Add to the current model group
                currentModel.add(model);
            },
            function (xhr) {
                // Progress callback
                console.log((xhr.loaded / xhr.total * 100) + '% loaded');
            },
            function (error) {
                // Error callback
                console.error('Error loading model:', error);

                // Fallback to placeholder for this part
                createPlaceholderPart(partType);
            }
        );
    }

    // Position watch part based on type
    function positionWatchPart(partType, model) {
        switch (partType) {
            case 'case':
                // No adjustment needed, this is the base
                break;
            case 'dial':
                model.position.z = 0.1;
                break;
            case 'hands':
                model.position.z = 0.2;
                break;
            case 'strap':
                model.position.y = -2;
                break;
            case 'movement':
                model.position.z = -0.2;
                model.rotation.y = Math.PI;
                break;
        }
    }

    // Create a placeholder for missing parts
    function createPlaceholderPart(partType) {
        if (!THREE) return;

        let geometry, material, mesh;

        switch (partType) {
            case 'case':
                geometry = new THREE.CylinderGeometry(4, 4, 1, 32);
                material = new THREE.MeshStandardMaterial({ color: 0x888888 });
                mesh = new THREE.Mesh(geometry, material);
                mesh.rotation.x = Math.PI * 0.5;
                break;
            case 'dial':
                geometry = new THREE.CircleGeometry(3.5, 32);
                material = new THREE.MeshStandardMaterial({ color: 0xFFFFFF });
                mesh = new THREE.Mesh(geometry, material);
                mesh.position.z = 0.1;
                break;
            case 'hands':
                const hourHandGeo = new THREE.BoxGeometry(0.2, 2, 0.1);
                const minuteHandGeo = new THREE.BoxGeometry(0.2, 3, 0.1);
                const handMaterial = new THREE.MeshStandardMaterial({ color: 0x000000 });

                const hourHand = new THREE.Mesh(hourHandGeo, handMaterial);
                hourHand.position.y = 1;

                const minuteHand = new THREE.Mesh(minuteHandGeo, handMaterial);
                minuteHand.position.y = 1.5;
                minuteHand.rotation.z = Math.PI * 0.25;

                mesh = new THREE.Group();
                mesh.add(hourHand);
                mesh.add(minuteHand);
                mesh.position.z = 0.2;
                break;
            case 'strap':
                geometry = new THREE.BoxGeometry(2, 8, 0.5);
                material = new THREE.MeshStandardMaterial({ color: 0x000000 });
                mesh = new THREE.Mesh(geometry, material);
                mesh.position.y = -2;
                break;
            case 'movement':
                geometry = new THREE.CylinderGeometry(3.5, 3.5, 0.5, 32);
                material = new THREE.MeshStandardMaterial({ color: 0x444444 });
                mesh = new THREE.Mesh(geometry, material);
                mesh.position.z = -0.5;
                mesh.rotation.x = Math.PI * 0.5;
                break;
        }

        if (mesh && currentModel) {
            currentModel.add(mesh);
        }
    }

    // Create a 2D watch model using canvas fallback
    function createWatchModel2D() {
        if (!ctx || !canvas) return;

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Set center point
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;

        // Draw based on current view
        switch (currentView) {
            case 'front':
                drawWatchFront(centerX, centerY);
                break;
            case 'side':
                drawWatchSide(centerX, centerY);
                break;
            case 'back':
                drawWatchBack(centerX, centerY);
                break;
            case '3d':
                // In 2D mode, just show front view for "3D" option
                drawWatchFront(centerX, centerY);
                break;
        }
    }

    // Draw front view of watch in 2D
    function drawWatchFront(centerX, centerY) {
        // Draw case
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.arc(centerX, centerY, 150, 0, Math.PI * 2);
        ctx.stroke();
        ctx.fillStyle = '#FFFFFF';
        ctx.fill();

        // Draw lugs
        ctx.lineWidth = 4;
        ctx.beginPath();
        // Top lugs
        ctx.moveTo(centerX - 50, centerY - 150);
        ctx.lineTo(centerX - 80, centerY - 200);
        ctx.moveTo(centerX + 50, centerY - 150);
        ctx.lineTo(centerX + 80, centerY - 200);
        // Bottom lugs
        ctx.moveTo(centerX - 50, centerY + 150);
        ctx.lineTo(centerX - 80, centerY + 200);
        ctx.moveTo(centerX + 50, centerY + 150);
        ctx.lineTo(centerX + 80, centerY + 200);
        ctx.stroke();

        // Draw dial
        ctx.fillStyle = '#EEEEEE';
        ctx.beginPath();
        ctx.arc(centerX, centerY, 130, 0, Math.PI * 2);
        ctx.fill();
        ctx.stroke();

        // Draw hour markers
        ctx.fillStyle = '#000000';
        for (let i = 0; i < 12; i++) {
            const angle = (i * Math.PI / 6) - Math.PI / 2;
            const markerX = centerX + 110 * Math.cos(angle);
            const markerY = centerY + 110 * Math.sin(angle);

            ctx.beginPath();
            if (i % 3 === 0) {
                // Quarter hour markers
                ctx.rect(markerX - 5, markerY - 5, 10, 10);
            } else {
                // Regular hour markers
                ctx.arc(markerX, markerY, 3, 0, Math.PI * 2);
            }
            ctx.fill();
        }

        // Draw hands
        const date = new Date();
        const hours = date.getHours() % 12;
        const minutes = date.getMinutes();
        const seconds = date.getSeconds();

        // Hour hand
        const hourAngle = (hours + minutes / 60) * Math.PI / 6 - Math.PI / 2;
        ctx.lineWidth = 6;
        ctx.strokeStyle = '#000000';
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(
            centerX + 70 * Math.cos(hourAngle),
            centerY + 70 * Math.sin(hourAngle)
        );
        ctx.stroke();

        // Minute hand
        const minuteAngle = minutes * Math.PI / 30 - Math.PI / 2;
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(
            centerX + 90 * Math.cos(minuteAngle),
            centerY + 90 * Math.sin(minuteAngle)
        );
        ctx.stroke();

        // Second hand
        const secondAngle = seconds * Math.PI / 30 - Math.PI / 2;
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#FF0000';
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(
            centerX + 100 * Math.cos(secondAngle),
            centerY + 100 * Math.sin(secondAngle)
        );
        ctx.stroke();

        // Draw center cap
        ctx.fillStyle = '#000000';
        ctx.beginPath();
        ctx.arc(centerX, centerY, 5, 0, Math.PI * 2);
        ctx.fill();

        // Draw strap
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.fillStyle = '#222222';

        // Top strap
        ctx.beginPath();
        ctx.rect(centerX - 40, centerY - 250, 80, 50);
        ctx.fill();
        ctx.stroke();

        // Bottom strap
        ctx.beginPath();
        ctx.rect(centerX - 40, centerY + 200, 80, 50);
        ctx.fill();
        ctx.stroke();
    }

    // Draw side view of watch in 2D
    function drawWatchSide(centerX, centerY) {
        // Draw watch profile
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#000000';
        ctx.fillStyle = '#CCCCCC';

        // Case profile
        ctx.beginPath();
        ctx.rect(centerX - 150, centerY - 20, 300, 40);
        ctx.fill();
        ctx.stroke();

        // Crown
        ctx.beginPath();
        ctx.rect(centerX + 150, centerY - 5, 20, 10);
        ctx.fill();
        ctx.stroke();

        // Strap
        ctx.fillStyle = '#222222';

        // Left strap
        ctx.beginPath();
        ctx.rect(centerX - 300, centerY - 10, 150, 20);
        ctx.fill();
        ctx.stroke();

        // Right strap
        ctx.beginPath();
        ctx.rect(centerX + 150, centerY - 10, 150, 20);
        ctx.fill();
        ctx.stroke();

        // Case details
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(centerX - 150, centerY - 10);
        ctx.lineTo(centerX + 150, centerY - 10);
        ctx.moveTo(centerX - 150, centerY + 10);
        ctx.lineTo(centerX + 150, centerY + 10);
        ctx.stroke();
    }

    // Draw back view of watch in 2D
    function drawWatchBack(centerX, centerY) {
        // Draw case back
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.arc(centerX, centerY, 150, 0, Math.PI * 2);
        ctx.stroke();
        ctx.fillStyle = '#CCCCCC';
        ctx.fill();

        // Draw case back details
        ctx.beginPath();
        ctx.arc(centerX, centerY, 120, 0, Math.PI * 2);
        ctx.stroke();

        // Draw screw points
        for (let i = 0; i < 6; i++) {
            const angle = i * Math.PI / 3;
            const screwX = centerX + 135 * Math.cos(angle);
            const screwY = centerY + 135 * Math.sin(angle);

            ctx.beginPath();
            ctx.arc(screwX, screwY, 5, 0, Math.PI * 2);
            ctx.fill();
            ctx.stroke();
        }

        // Draw case back text
        ctx.font = '14px Arial';
        ctx.fillStyle = '#000000';
        ctx.textAlign = 'center';
        ctx.fillText('WATCHMODMARKET', centerX, centerY - 40);
        ctx.fillText('STAINLESS STEEL', centerX, centerY - 20);
        ctx.fillText('WATER RESISTANT 100M', centerX, centerY);
        ctx.fillText('CUSTOM MADE', centerX, centerY + 20);
        ctx.fillText('SERIAL: WMM' + Math.floor(Math.random() * 1000000), centerX, centerY + 40);

        // Draw lugs
        ctx.lineWidth = 4;
        ctx.beginPath();
        // Top lugs
        ctx.moveTo(centerX - 50, centerY - 150);
        ctx.lineTo(centerX - 80, centerY - 200);
        ctx.moveTo(centerX + 50, centerY - 150);
        ctx.lineTo(centerX + 80, centerY - 200);
        // Bottom lugs
        ctx.moveTo(centerX - 50, centerY + 150);
        ctx.lineTo(centerX - 80, centerY + 200);
        ctx.moveTo(centerX + 50, centerY + 150);
        ctx.lineTo(centerX + 80, centerY + 200);
        ctx.stroke();

        // Draw strap
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.fillStyle = '#222222';

        // Top strap
        ctx.beginPath();
        ctx.rect(centerX - 40, centerY - 250, 80, 50);
        ctx.fill();
        ctx.stroke();

        // Bottom strap
        ctx.beginPath();
        ctx.rect(centerX - 40, centerY + 200, 80, 50);
        ctx.fill();
        ctx.stroke();
    }

    // Update the camera angle based on selected view
    function updateViewAngle() {
        if (!camera) return;

        // Update auto-rotation based on view
        if (controls) {
            controls.autoRotate = (currentView === '3d');
        }

        // Set camera position based on view
        switch (currentView) {
            case 'front':
                camera.position.set(0, 0, 10);
                break;
            case 'side':
                camera.position.set(10, 0, 0);
                break;
            case 'back':
                camera.position.set(0, 0, -10);
                break;
            case '3d':
                camera.position.set(7, 7, 7);
                break;
        }

        // Look at the center
        camera.lookAt(0, 0, 0);

        // Reset controls if available
        if (controls) {
            controls.update();
        }
    }

    // Check compatibility between selected parts
    function checkCompatibility() {
        compatibilityWarnings = [];

        // Check case and movement compatibility
        if (selectedParts.case && selectedParts.movement) {
            const caseId = selectedParts.case.split('-')[1];
            const movementId = selectedParts.movement.split('-')[1];

            // Get compatibility data from data attributes
            const $caseElement = $(`[data-part-id="case-${caseId}"]`);

            if ($caseElement.length) {
                const caseCompat = $caseElement.data('compatibility');

                if (caseCompat && typeof caseCompat === 'string' && !caseCompat.includes(`movement-${movementId}`)) {
                    compatibilityWarnings.push({
                        parts: ['case', 'movement'],
                        message: getLocalizedText('incompatibleMovement', 'The selected movement is not compatible with this case.')
                    });
                }
            }
        }

        // Check case and dial compatibility
        if (selectedParts.case && selectedParts.dial) {
            const caseId = selectedParts.case.split('-')[1];
            const dialId = selectedParts.dial.split('-')[1];

            // Get data from selected parts
            const $caseElement = $(`[data-part-id="case-${caseId}"]`);
            const $dialElement = $(`[data-part-id="dial-${dialId}"]`);

            if ($caseElement.length && $dialElement.length) {
                const caseDiameter = $caseElement.data('diameter');
                const dialDiameter = $dialElement.data('diameter');

                if (caseDiameter && dialDiameter && caseDiameter < dialDiameter) {
                    compatibilityWarnings.push({
                        parts: ['case', 'dial'],
                        message: getLocalizedText('dialTooBig', 'The selected dial is too large for this case.')
                    });
                }
            }
        }

        // Check hands and movement compatibility
        if (selectedParts.hands && selectedParts.movement) {
            const handsId = selectedParts.hands.split('-')[1];
            const movementId = selectedParts.movement.split('-')[1];

            const $handsElement = $(`[data-part-id="hands-${handsId}"]`);
            const $movementElement = $(`[data-part-id="movement-${movementId}"]`);

            if ($handsElement.length && $movementElement.length) {
                const handsType = $handsElement.data('type');
                const movementType = $movementElement.data('type');

                if (handsType && movementType && handsType !== movementType) {
                    compatibilityWarnings.push({
                        parts: ['hands', 'movement'],
                        message: getLocalizedText('incompatibleHands', 'The selected hands are not compatible with this movement.')
                    });
                }
            }
        }

        // Update compatibility warnings display
        updateCompatibilityWarnings();
    }

    // Get localized text or fallback to default
    function getLocalizedText(key, defaultText) {
        return (window.wpData && window.wpData.i18n && window.wpData.i18n[key]) ?
            window.wpData.i18n[key] : defaultText;
    }

    // Update compatibility warnings display
    function updateCompatibilityWarnings() {
        const $alert = $('#compatibility-alert');

        if (!$alert.length) return;

        if (compatibilityWarnings.length > 0) {
            let warningHtml = '<ul>';
            compatibilityWarnings.forEach(warning => {
                warningHtml += `<li>${warning.message}</li>`;
            });
            warningHtml += '</ul>';

            // Update alert content
            $alert.find('p').html(warningHtml);

            // Show alert
            $alert.slideDown();
        } else {
            // Hide alert if no warnings
            $alert.slideUp();
        }
    }

    // Update total price
    function updatePricing() {
        let totalPrice = 0;

        // Add up prices of selected parts
        Object.keys(selectedParts).forEach(partType => {
            if (selectedParts[partType]) {
                const partId = selectedParts[partType].split('-')[1];
                const $part = $(`[data-part-id="${partType}-${partId}"]`);

                if ($part.length > 0) {
                    const partPrice = parseFloat($part.data('price')) || 0;
                    totalPrice += partPrice;
                }
            }
        });

        // Update price display
        const $totalPrice = $('#total-price');
        if ($totalPrice.length) {
            // Check if a currency symbol is provided in wpData
            const currencySymbol = window.wpData && window.wpData.currencySymbol ?
                window.wpData.currencySymbol : ''
                ;

            $totalPrice.text(currencySymbol + totalPrice.toFixed(2));
        }

        // Update hidden input for form submission
        const $buildTotalPrice = $('#build-total-price');
        if ($buildTotalPrice.length) {
            $buildTotalPrice.val(totalPrice.toFixed(2));
        }

        // Update spec table if it exists
        updateSpecTable();
    }

    // Update specification table
    function updateSpecTable() {
        const specTable = $(".spec-table");
        if (!specTable.length) return;

        // Clear existing specs
        specTable.find("tbody").empty();

        // Add specs for each selected part
        Object.keys(selectedParts).forEach(function (partType) {
            if (selectedParts[partType]) {
                const partId = selectedParts[partType].split("-")[1];
                const part = $("[data-part-id='" + partType + "-" + partId + "']");

                if (part.length > 0) {
                    const partName = part.find(".part-name").text();
                    const partSpecs = part.find(".part-specs").text();

                    // Add row to spec table - no template literals, use string concatenation
                    const row = "<tr>" +
                        "<td>" + capitalizeFirstLetter(partType) + "</td>" +
                        "<td>" + partName + "</td>" +
                        "<td>" + partSpecs + "</td>" +
                        "</tr>";

                    specTable.find("tbody").append(row);
                }
            }
        });
    }

    // Capitalize first letter of a string
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Handle form submission
    function setupFormSubmission() {
        $("#add-build-to-cart").on("submit", function (e) {
            // Validate that all required parts are selected
            const requiredParts = ["case", "dial", "hands", "strap", "movement"];
            const missingParts = [];

            requiredParts.forEach(part => {
                if (!selectedParts[part]) {
                    missingParts.push(getLocalizedText(part, capitalizeFirstLetter(part)));
                }
            });

            if (missingParts.length > 0) {
                e.preventDefault();
                alert(getLocalizedText("selectAllParts", "Please select all required parts") + ": " + missingParts.join(", "));
                return false;
            }

            // Check compatibility warnings
            if (compatibilityWarnings.length > 0 && !confirm(getLocalizedText("confirmIncompatible", "There are compatibility warnings. Do you still want to add this build to cart?"))) {
                e.preventDefault();
                return false;
            }

            // Add selected parts to hidden inputs
            Object.keys(selectedParts).forEach(partType => {
                if (selectedParts[partType]) {
                    const partId = selectedParts[partType].split("-")[1];
                    const hiddenInput = $("<input type='hidden' name='selected_" + partType + "' value='" + partId + "'>");
                    $(this).append(hiddenInput);
                }
            });

            // Add to cart if all validations pass
            return true;
        });
    }

    // Save current build
    function setupSaveButton() {
        $("#save-build").on("click", function (e) {
            e.preventDefault();

            // Check if user is logged in
            if (window.wpData && !window.wpData.isLoggedIn) {
                alert(getLocalizedText("loginRequired", "You must be logged in to save builds. Please log in or create an account."));
                window.location.href = window.wpData.loginUrl || "/my-account/";
                return;
            }

            // Validate that at least some parts are selected
            let hasSelectedParts = false;
            Object.keys(selectedParts).forEach(partType => {
                if (selectedParts[partType]) {
                    hasSelectedParts = true;
                }
            });

            if (!hasSelectedParts) {
                alert(getLocalizedText("selectSomeParts", "Please select at least one part to save a build."));
                return;
            }

            // If all checks pass, open save dialog
            openSaveDialog();
        });
    }

    // Open save dialog
    function openSaveDialog() {
        // Create modal if it doesn't exist
        if (!$("#save-build-modal").length) {
            // Create modal HTML without template literals
            const modalHtml =
                '<div id="save-build-modal" class="builder-modal">' +
                '<div class="builder-modal-content">' +
                '<span class="builder-modal-close">&times;</span>' +
                '<h3>' + getLocalizedText("saveBuild", "Save Your Build") + '</h3>' +
                '<form id="save-build-form">' +
                '<div class="form-group">' +
                '<label for="build-name">' + getLocalizedText("buildName", "Build Name") + '</label>' +
                '<input type="text" id="build-name" name="build_name" required>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="build-description">' + getLocalizedText("buildDescription", "Description (Optional)") + '</label>' +
                '<textarea id="build-description" name="build_description"></textarea>' +
                '</div>' +
                '<div class="form-group">' +
                '<label>' +
                '<input type="checkbox" name="build_public" value="1">' +
                getLocalizedText("makePublic", "Make this build public in the community") +
                '</label>' +
                '</div>' +
                '<button type="submit" class="btn btn-primary">' + getLocalizedText("saveButton", "Save Build") + '</button>' +
                '</form>' +
                '</div>' +
                '</div>';

            const modal = $(modalHtml);

            $("body").append(modal);

            // Handle close button
            $(".builder-modal-close").on("click", function () {
                $("#save-build-modal").hide();
            });

            // Handle click outside modal
            $(window).on("click", function (e) {
                if ($(e.target).is("#save-build-modal")) {
                    $("#save-build-modal").hide();
                }
            });

            // Handle form submission
            $("#save-build-form").on("submit", function (e) {
                e.preventDefault();
                saveBuildToServer();
            });
        }

        // Show the modal
        $("#save-build-modal").show();
    }

    // Save build to server
    function saveBuildToServer() {
        // Get form data
        const buildName = $("#build-name").val();
        const buildDescription = $("#build-description").val();
        const isPublic = $("#save-build-form input[name='build_public']").is(":checked") ? 1 : 0;

        // Create data object
        const data = {
            action: "save_watch_build",
            security: window.wpData ? window.wpData.nonce : "",
            build_name: buildName,
            build_description: buildDescription,
            build_public: isPublic,
            selected_parts: selectedParts
        };

        // Show loading message
        $("#save-build-form button").prop("disabled", true).text(getLocalizedText("saving", "Saving..."));

        // Send AJAX request
        $.ajax({
            url: window.wpData ? window.wpData.ajaxUrl : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: data,
            success: function (response) {
                $("#save-build-modal").hide();

                if (response.success) {
                    alert(getLocalizedText("buildSaved", "Your build has been saved successfully!"));
                } else {
                    alert(response.data.message || getLocalizedText("saveFailed", "Failed to save your build. Please try again."));
                }

                // Reset form
                $("#save-build-form")[0].reset();
                $("#save-build-form button").prop("disabled", false).text(getLocalizedText("saveButton", "Save Build"));
            },
            error: function () {
                alert(getLocalizedText("saveFailed", "Failed to save your build. Please try again."));
                $("#save-build-form button").prop("disabled", false).text(getLocalizedText("saveButton", "Save Build"));
            }
        });
    }

    // Clean up resources when leaving the page
    function cleanup() {
        // Cancel animation frame to prevent memory leaks
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        // Dispose Three.js resources if they exist
        if (scene) {
            if (currentModel) {
                scene.remove(currentModel);
                disposeObject(currentModel);
            }

            disposeObject(scene);
        }

        if (renderer) {
            renderer.dispose();
            renderer = null;
        }
    }

    // Helper function to dispose Three.js objects
    function disposeObject(obj) {
        if (!obj) return;

        // Dispose geometries and materials
        if (obj.geometry) {
            obj.geometry.dispose();
        }

        if (obj.material) {
            if (Array.isArray(obj.material)) {
                obj.material.forEach(material => material.dispose());
            } else {
                obj.material.dispose();
            }
        }

        // Recursively dispose child objects
        if (obj.children && obj.children.length > 0) {
            for (let i = obj.children.length - 1; i >= 0; i--) {
                disposeObject(obj.children[i]);
            }
        }
    }

    // Initialize on document ready
    $(document).ready(function () {
        initBuilder();
        setupFormSubmission();
        setupSaveButton();

        // Add event listener for page unload
        $(window).on("beforeunload", cleanup);
    });

})(jQuery);