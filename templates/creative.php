<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CV: <?php echo htmlspecialchars($cv['full_name'] ?? ''); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <style>
        /* ATS-Friendly Creative Template */
        body {
            font-family: 'Roboto', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #2d3748;
            margin: 0;
            padding: 0;
        }
        .cv-container {
            color: black;
            <?php if (!empty($is_pdf)): ?>
                padding: 0.7in;
                margin: 0;
                box-shadow: none;
            <?php else: ?>
                max-width: 8.5in;
                min-height: 11in;
                margin: 2rem auto;
                padding: 1in;
                box-shadow: 0 0 12px rgba(0,0,0,0.08);
            <?php endif; ?>
        }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 {
            font-family: 'Roboto Slab', serif;
            font-size: 28pt;
            margin: 0 0 5px 0;
            color: #1a202c; /* Very Dark Gray */
        }
        .header .target-role {
            font-size: 14pt;
            font-weight: 500;
            color: #008080; /* Teal Accent */
            margin: 0 0 15px 0;
        }
        .contact-info { font-size: 10pt; }
        .contact-info span { display: inline-block; }
        .contact-info span:not(:last-child)::after { content: " | "; margin: 0 0.5em; color: #a0aec0; }
        .contact-info a { color: #2d3748; text-decoration: none; }
        .contact-info a:hover { text-decoration: underline; }

        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section h2 {
            font-family: 'Roboto Slab', serif;
            font-size: 14pt;
            color: #008080; /* Teal Accent */
            margin: 0 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0; /* Light Gray */
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .entry { margin-bottom: 15px; page-break-inside: avoid; }
        .entry-header { display: block; margin-bottom: 2px; }
        .entry-title { font-size: 12pt; font-weight: 700; color: #1a202c; }
        .entry-meta { float: right; font-size: 10pt; font-weight: 500; color: #4a5568; }
        .entry-subtitle { font-size: 11pt; font-weight: 500; color: #4a5568; margin-bottom: 5px; }
        .entry ul { margin: 5px 0 0; padding-left: 20px; }
        .entry li { margin-bottom: 4px; }
        
        .skills-list { margin-top: 5px; line-height: 1.8; }
        .summary-text { text-align: justify; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="cv-container">
        <header class="header">
            <h1><?php echo htmlspecialchars($cv['full_name'] ?? ''); ?></h1>
            <p class="target-role"><?php echo htmlspecialchars($cv['target_role'] ?? ''); ?></p>
            <div class="contact-info">
                <?php if (!empty($cv['address'])): ?><span><?php echo htmlspecialchars($cv['address']); ?></span><?php endif; ?>
                <?php if (!empty($cv['phone'])): ?><span><?php echo htmlspecialchars($cv['phone']); ?></span><?php endif; ?>
                <?php if (!empty($cv['email'])): ?><span><a href="mailto:<?php echo htmlspecialchars($cv['email']); ?>"><?php echo htmlspecialchars($cv['email']); ?></a></span><?php endif; ?>
                <?php if (!empty($cv['linkedin_url'])): ?><span><a href="<?php echo htmlspecialchars($cv['linkedin_url']); ?>">LinkedIn</a></span><?php endif; ?>
                <?php if (!empty($cv['github_url'])): ?><span><a href="<?php echo htmlspecialchars($cv['github_url']); ?>">GitHub</a></span><?php endif; ?>
            </div>
        </header>

        <?php if (!empty($cv['summary'])): ?>
        <section class="section">
            <h2>Summary</h2>
            <p class="summary-text"><?php echo nl2br(htmlspecialchars($cv['summary'])); ?></p>
        </section>
        <?php endif; ?>

        <?php if ($experiences->rowCount() > 0): ?>
        <section class="section">
            <h2>Experience</h2>
            <?php while ($exp = $experiences->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="entry">
                <div class="entry-header clearfix">
                    <span class="entry-meta"><?php echo htmlspecialchars($exp['start_date']); ?> – <?php echo htmlspecialchars($exp['end_date'] ?: 'Present'); ?></span>
                    <span class="entry-title"><?php echo htmlspecialchars($exp['job_title']); ?></span>
                </div>
                <div class="entry-subtitle"><?php echo htmlspecialchars($exp['company_name']); ?></div>
                <?php if (!empty($exp['description'])): ?>
                <ul>
                    <?php foreach(explode("\n", trim($exp['description'])) as $point): ?>
                        <?php if(trim($point)): ?><li><?php echo htmlspecialchars(trim($point)); ?></li><?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </section>
        <?php endif; ?>

        <?php if ($projects->rowCount() > 0): ?>
        <section class="section">
            <h2>Projects</h2>
            <?php while ($proj = $projects->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="entry">
                <div class="entry-header clearfix">
                    <span class="entry-title"><?php echo htmlspecialchars($proj['project_name']); ?></span>
                    <?php if (!empty($proj['project_url'])): ?>
                        <span class="entry-meta"><a href="<?php echo htmlspecialchars($proj['project_url']); ?>">View Project</a></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($proj['description'])): ?>
                <ul>
                    <?php foreach(explode("\n", trim($proj['description'])) as $point): ?>
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
                <div class="entry-header clearfix">
                    <span class="entry-meta"><?php echo htmlspecialchars($edu['start_date']); ?> – <?php echo htmlspecialchars($edu['end_date']); ?></span>
                    <span class="entry-title"><?php echo htmlspecialchars($edu['degree']); ?></span>
                </div>
                <div class="entry-subtitle"><?php echo htmlspecialchars($edu['institution']); ?></div>
            </div>
            <?php endwhile; ?>
        </section>
        <?php endif; ?>
        
        <?php if ($certificates->rowCount() > 0): ?>
        <section class="section">
            <h2>Certificates</h2>
            <?php while ($cert = $certificates->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="entry">
                 <div class="entry-header clearfix">
                    <span class="entry-meta"><?php echo htmlspecialchars($cert['issue_date']); ?></span>
                    <span class="entry-title"><?php echo htmlspecialchars($cert['certificate_name']); ?></span>
                </div>
                <div class="entry-subtitle">
                    <?php echo htmlspecialchars($cert['issuing_organization']); ?>
                    <?php if (!empty($cert['credential_url'])): ?>
                         | <a href="<?php echo htmlspecialchars($cert['credential_url']); ?>">Show Credential</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </section>
        <?php endif; ?>

        <?php if ($skills->rowCount() > 0): ?>
        <section class="section">
            <h2>Core Skills</h2>
            <p class="skills-list">
                <?php 
                $skill_array = [];
                while ($skill = $skills->fetch(PDO::FETCH_ASSOC)) {
                    $skill_array[] = htmlspecialchars($skill['skill_name']);
                }
                echo implode(' &bull; ', $skill_array);
                ?>
            </p>
        </section>
        <?php endif; ?>
    </div>
</body>
</html>