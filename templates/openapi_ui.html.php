<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@latest/swagger-ui.min.css"/>
    <style>
        body {
            background-color: white;
        }
    </style>
</head>
<body>
<?php
if ($validation_errors) { ?>
    <div class="swagger-ui">
        <div class="wrapper">
            <div class="block col-12 block-desktop col-12-desktop">
                <section class="errors-wrapper" style="margin-left: 0; margin-right: 0">
                    <div class="error">
                        <h4 class="errors__title">Validation error</h4>
                    </div>
                    <div class="no-margin">
                        <div class="errors">
                            <div class="error-wrapper">
                                <pre class="message thrown"><?= $validation_errors ?></pre>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php
} ?>
<div id="swagger-ui"></div>
<script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@latest/swagger-ui-bundle.min.js"></script>
<script>
    window.onload = () => {
        window.ui = SwaggerUIBundle({
            url: '<?= $url ?>',
            dom_id: '#swagger-ui',
        });
    };
</script>
</body>
</html>
