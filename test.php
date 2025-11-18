<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CV: <?php echo htmlspecialchars($cv['full_name'] ?? 'Untitled'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'EB Garamond', Garamond, serif;
            font-size: 11.5pt;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
        }
        .cv-container {
            <?php if (!empty($is_pdf)): ?>
                padding: 0.7in;
                margin: 0;
                box-shadow: none;
            <?php else: ?>
                max-width: 8.5in;
                min-height: 11in;
                margin: 2rem auto;
                padding: 1in;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            <?php endif; ?>
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .header h1 {
            font-size: 2em;
            margin: 0;
            font-weight: 600;
        }
        .contact-info {
            margin-top: 0.5rem;
            font-size: 0.9em;
            color: #333;
        }
        .contact-info span:not(:last-child)::after {
            content: "•";
            margin: 0 0.5em;
            color: #888;
        }
        .section { margin-bottom: 1.6rem; }
        .section h2 {
            font-size: 1.05em;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 0.2em;
            margin-bottom: 0.8rem;
        }
        .entry { margin-bottom: 1rem; }
        .entry-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.95em;
            margin-bottom: 0.3rem;
        }
        .entry-title { font-weight: 600; }
        .entry-meta { font-size: 0.85em; color: #555; }
        .entry-subtitle { font-weight: 600; margin-bottom: 0.3rem; }
        .entry-description {
            margin: 0;
            padding-left: 1.2em;
        }
        .entry-description li { margin-bottom: 0.25rem; font-weight: normal; }
        .skills-list {
            columns: 2;
            list-style: disc;
            padding-left: 1.2em;
        }
        .skills-list li { margin-bottom: 0.3rem; font-weight: 500; }
    </style>
</head>
<body>
<div class="cv-container">
    <header class="header">
        <h1><?php echo htmlspecialchars($cv['full_name'] ?? 'Your Name'); ?></h1>
        <div class="contact-info">
            <?php if (!empty($cv['phone'])): ?><span><?php echo htmlspecialchars($cv['phone']); ?></span><?php endif; ?>
            <?php if (!empty($cv['email'])): ?><span><?php echo htmlspecialchars($cv['email']); ?></span><?php endif; ?>
            <?php if (!empty($cv['address'])): ?><span><?php echo htmlspecialchars($cv['address']); ?></span><?php endif; ?>
        </div>
    </header>

    <?php if (!empty($cv['summary'])): ?>
    <section class="section">
        <h2>Professional Summary</h2>
        <p><?php echo nl2br(htmlspecialchars($cv['summary'])); ?></p>
    </section>
    <?php endif; ?>

    <?php if ($experiences->rowCount() > 0): ?>
    <section class="section">
        <h2>Experience</h2>
        <?php while ($exp = $experiences->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="entry">
            <div class="entry-header">
                <span class="entry-title"><?php echo htmlspecialchars($exp['job_title']); ?></span>
                <span class="entry-meta"><?php echo htmlspecialchars($exp['start_date']); ?> – <?php echo htmlspecialchars($exp['end_date'] ?: 'Present'); ?></span>
            </div>
            <div class="entry-subtitle"><?php echo htmlspecialchars($exp['company_name']); ?><?php if(!empty($exp['location'])) echo " • " . htmlspecialchars($exp['location']); ?></div>
            <?php if (!empty($exp['description'])): ?>
                <ul class="entry-description">
                    <?php foreach(explode("\n", trim($exp['description'])) as $point): ?>
                        <?php if(trim($point)): ?><li><?php echo htmlspecialchars(trim($point)); ?></li><?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </section>
    <?php endif; ?>

    <?php if ($educations->rowCount() > 0): ?>
    <section class="section">
        <h2>Education</h2>
        <?php while ($edu = $educations->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="entry">
            <div class="entry-header">
                <span class="entry-title"><?php echo htmlspecialchars($edu['degree']); ?></span>
                <span class="entry-meta"><?php echo htmlspecialchars($edu['start_date']); ?> – <?php echo htmlspecialchars($edu['end_date']); ?></span>
            </div>
            <div class="entry-subtitle"><?php echo htmlspecialchars($edu['institution']); ?></div>
        </div>
        <?php endwhile; ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($skills) && $skills->rowCount() > 0): ?>
    <section class="section">
        <h2>Skills</h2>
        <ul class="skills-list">
            <?php while ($skill = $skills->fetch(PDO::FETCH_ASSOC)): ?>
                <li><?php echo htmlspecialchars($skill['skill_name']); ?></li>
            <?php endwhile; ?>
        </ul>
    </section>
    <?php endif; ?>
</div>
</body>
</html>
