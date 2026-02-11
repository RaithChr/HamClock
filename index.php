<?php // OE3LCR Ham Radio Dashboard – index.php ?>
<?php include __DIR__ . '/includes/head.php'; ?>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/modals.php'; ?>

<div class="gs-container">
    <div class="grid-stack">
        <div class="grid-stack-item" id="widget-sun"
             gs-x="0" gs-y="0" gs-w="4" gs-h="5" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/sun.php'; ?>
            </div>
        </div>
        <div class="grid-stack-item" id="widget-qth"
             gs-x="4" gs-y="0" gs-w="4" gs-h="5" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/qth.php'; ?>
            </div>
        </div>
        <div class="grid-stack-item" id="widget-bands"
             gs-x="8" gs-y="0" gs-w="4" gs-h="5" gs-min-w="3">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/bands.php'; ?>
            </div>
        </div>
        <div class="grid-stack-item" id="widget-weather"
             gs-x="0" gs-y="5" gs-w="12" gs-h="4" gs-min-w="4">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/weather.php'; ?>
            </div>
        </div>
        <div class="grid-stack-item" id="widget-satellites"
             gs-x="0" gs-y="9" gs-w="4" gs-h="6" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/satellites.php'; ?>
            </div>
        </div>
        <div class="grid-stack-item" id="widget-dx"
             gs-x="4" gs-y="9" gs-w="4" gs-h="6" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/dx.php'; ?>
            </div>
        </div>
        <div class="grid-stack-item" id="widget-system"
             gs-x="8" gs-y="9" gs-w="4" gs-h="6" gs-min-w="2">
            <div class="grid-stack-item-content">
                <?php include __DIR__ . '/widgets/system.php'; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
