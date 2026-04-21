<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ritual PDF - <?= htmlspecialchars($ritual['name']) ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Karma:wght@400;500;600;700&family=Cinzel:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Karma', serif;
            color: #1F2937;
            background: white;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        #pdf-content {
            padding: 40px;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        h1 {
            font-family: 'Cinzel', serif;
            color: #047857;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .sanskrit {
            font-size: 1.2rem;
            color: #059669;
            font-style: italic;
            margin-bottom: 15px;
            display: block;
        }

        .meta {
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 0.9rem;
            color: #6B7280;
        }

        .section {
            margin-bottom: 30px;
        }

        h2 {
            font-size: 1.5rem;
            color: #064E3B;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-family: 'Cinzel', serif;
        }

        .items-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            list-style: none;
            padding: 0;
        }

        .items-list li {
            padding: 8px;
            background: #F9FAFB;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .step {
            margin-bottom: 20px;
            padding-left: 20px;
            border-left: 3px solid #D1FAE5;
        }

        .step-title {
            font-weight: 700;
            color: #065F46;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .step-mantra {
            background: #FDF2F8;
            padding: 10px;
            border-radius: 8px;
            font-style: italic;
            color: #831843;
            margin: 10px 0;
            font-family: 'Cinzel', serif;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #E5E7EB;
            border-top-color: #059669;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div id="loading" class="loading-overlay">
        <div class="spinner"></div>
        <h2>Generating PDF...</h2>
        <p>Please wait while we prepare your document.</p>
    </div>

    <div id="pdf-content">
        <div class="header">
            <h1><?= htmlspecialchars($ritual['name']) ?></h1>
            <?php if (!empty($ritual['name_sanskrit'])): ?>
                <span class="sanskrit"><?= htmlspecialchars($ritual['name_sanskrit']) ?></span>
            <?php endif; ?>
            
            <div class="meta">
                <span>Duration: <?= $ritual['duration_minutes'] ?> mins</span>
                <span>Difficulty: <?= ucfirst($ritual['difficulty']) ?></span>
                <?php if (!empty($ritual['deity'])): ?>
                    <span>Deity: <?= htmlspecialchars($ritual['deity']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($ritual['description'])): ?>
        <div class="section">
            <h2>About this Ritual</h2>
            <p><?= nl2br(htmlspecialchars($ritual['description'])) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($ritual['items'])): ?>
        <div class="section">
            <h2>Items Needed</h2>
            <ul class="items-list">
                <?php foreach ($ritual['items'] as $item): ?>
                    <li>
                        <strong><?= htmlspecialchars($item['item_name']) ?></strong>
                        <?php if ($item['quantity'] > 0): ?>
                            (<?= $item['quantity'] . ' ' . $item['unit'] ?>)
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($ritual['steps'])): ?>
        <div class="section">
            <h2>Ritual Steps</h2>
            <?php foreach ($ritual['steps'] as $step): ?>
                <div class="step">
                    <div class="step-title">
                        Step <?= $step['step_number'] ?>: <?= htmlspecialchars($step['title']) ?>
                    </div>
                    <?php if (!empty($step['description'])): ?>
                        <p><?= nl2br(htmlspecialchars($step['description'])) ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($step['mantra'])): ?>
                        <div class="step-mantra">
                            "<?= htmlspecialchars($step['mantra']) ?>"
                            <?php if (!empty($step['mantra_meaning'])): ?>
                                <br><small style="color: #9D174D; normal; margin-top: 5px; display: block;">Meaning: <?= htmlspecialchars($step['mantra_meaning']) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="footer" style="text-align: center; margin-top: 50px; color: #9CA3AF; font-size: 0.8rem;">
            Generated by Sanskar AI - Your Spiritual Companion
        </div>
    </div>

    <!-- HTML2PDF CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        window.onload = function() {
            const element = document.getElementById('pdf-content');
            const opt = {
                margin:       10,
                filename:     '<?= preg_replace("/[^a-zA-Z0-9]+/", "_", $ritual["name"]) ?>_Ritual.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generate PDF
            html2pdf().set(opt).from(element).save().then(function() {
                // Show completion message with back button
                document.getElementById('loading').innerHTML = `
                    <div style="text-align: center;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; color: #10B981; margin-bottom: 15px;"></i>
                        <h2 style="color: #065F46;">PDF Downloaded!</h2>
                        <p style="color: #6B7280; margin-bottom: 25px;">Your ritual PDF has been saved.</p>
                        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                            <button onclick="window.history.back()" style="padding: 14px 30px; background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-arrow-left"></i> Go Back
                            </button>
                            <a href="/user/my-rituals" style="padding: 14px 30px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-book-reader"></i> My Rituals
                            </a>
                        </div>
                    </div>
                `;

                // Try to close tab if it was opened as a popup/new tab
                setTimeout(function() {
                    if (window.opener || window.history.length <= 1) {
                        window.close();
                    }
                }, 3000);
            });
        };
    </script>
    <!-- Font Awesome for icons in the completion message -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
