<?php // OE3LCR Ham Radio Dashboard ?>
<?php include __DIR__ . '/includes/head.php'; ?>
<body>
<?php include __DIR__ . '/includes/modals.php'; ?>

<div class="gs-container">
    <div class="grid-stack">

        <!-- ROW 0: Header (full width) -->
        <div class="grid-stack-item" id="widget-header" gs-id="widget-header"
             gs-x="0" gs-y="0" gs-w="12" gs-h="2" gs-min-w="6" gs-min-h="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/header.php'; ?>
            </div>
        </div>

        <!-- ROW 1: Clock | Sun | QTH | Bands -->
        <div class="grid-stack-item" id="widget-clock" gs-id="widget-clock"
             gs-x="6" gs-y="2" gs-w="2" gs-h="5" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/clock.php'; ?>
            </div>
        </div>

        <div class="grid-stack-item" id="widget-sun" gs-id="widget-sun"
             gs-x="0" gs-y="2" gs-w="3" gs-h="5" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/sun.php'; ?>
            </div>
        </div>

        <div class="grid-stack-item" id="widget-qth" gs-id="widget-qth"
             gs-x="3" gs-y="2" gs-w="3" gs-h="5" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/qth.php'; ?>
            </div>
        </div>

        <div class="grid-stack-item" id="widget-bands" gs-id="widget-bands"
             gs-x="8" gs-y="2" gs-w="4" gs-h="5" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/bands.php'; ?>
            </div>
        </div>

        <!-- ROW 2: Local Weather | Space Weather -->
        <div class="grid-stack-item" id="widget-weather-local" gs-id="widget-weather-local"
             gs-x="0" gs-y="7" gs-w="4" gs-h="4" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/weather-local.php'; ?>
            </div>
        </div>

        <div class="grid-stack-item" id="widget-weather-space" gs-id="widget-weather-space"
             gs-x="4" gs-y="7" gs-w="8" gs-h="4" gs-min-w="3">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/weather-space.php'; ?>
            </div>
        </div>

        <!-- ROW 3: Satellites | DX | System -->
        <div class="grid-stack-item" id="widget-satellites" gs-id="widget-satellites"
             gs-x="0" gs-y="11" gs-w="4" gs-h="6" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/satellites.php'; ?>
            </div>
        </div>

        <div class="grid-stack-item" id="widget-dx" gs-id="widget-dx"
             gs-x="4" gs-y="11" gs-w="4" gs-h="6" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/dx.php'; ?>
            </div>
        </div>

        <div class="grid-stack-item" id="widget-system" gs-id="widget-system"
             gs-x="8" gs-y="11" gs-w="4" gs-h="6" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/system.php'; ?>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
