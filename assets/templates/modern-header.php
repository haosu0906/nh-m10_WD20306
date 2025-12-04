<!-- Modern Header Template -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $page_title ?? 'Quản trị Tripmate' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="<?= BASE_URL ?>assets/css/modern-ui.css" rel="stylesheet" />
    <style>
    /* Using modern-ui.css for sidebar and main styles. Keep header minimal. */

    @media (max-width:900px) {
        .sidebar {
            position: relative;
            width: 100%
        }

        .main {
            margin-left: 0
        }
    }
    </style>
</head>
