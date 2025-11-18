<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CV: <?php echo htmlspecialchars($cv['full_name'] ?? ''); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
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
        .header h1 { 
            font-size: 32px; 
            font-weight: bold; 
            margin: 0 0 5px 0; 
            color: #111; 
        }
        
        .header p.role { 
            font-size: 24px; 
            font-weight: 600; 
            color: #2563eb; 
            margin: 0 0 15px 0; 
        }
        
        /* PDF-friendly contact info using table layout */
        .contact-info {
            width: 100%;
        }
        
        .contact-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .contact-info td {
            padding: 2px 15px 2px 0;
            font-size: 12px;
            color: #100f0f;
            vertical-align: top;
        }
        
        .contact-info a { 
            color: #100f0f; 
            text-decoration: none; 
        }

        .section { 
            margin-bottom: 20px; 
            page-break-inside: avoid;
        }
        
        .section h2 {
            font-size: 18px; 
            font-weight: bold;
            text-transform: uppercase; 
            color: #2563eb;
            border-bottom: 1px solid #ddd;
            margin-bottom: 12px; 
            padding-bottom: 4px;
        }
        
        .entry { 
            margin-bottom: 15px; 
            page-break-inside: avoid;
        }
        
        .entry-header { 
            margin-bottom: 3px; 
        }
        
        .entry-header table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .entry-title { 
            font-weight: bold; 
            font-size: 16px;
        }
        
        .entry-meta { 
            font-size: 12px;
            color: #100f0f; 
            font-style: italic; 
            text-align: right;
        }
        
        .entry-subtitle { 
            font-weight: 600; 
            margin-bottom: 5px; 
            font-size: 14px;
            color: #3d3b3b;
        }
        
        .entry ul { 
            margin: 5px 0 0 0; 
            padding-left: 12px; 
        }
        
        .entry li { 
            margin-bottom: 3px; 
            font-size: 14px;
            line-height: 1.4;
        }

        /* PDF-friendly skills list using table */
        .skills-container {
            width: 100%;
        }
        
        .skills-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .skills-table td {
            padding: 3px 8px 3px 0;
            vertical-align: top;
            width: 33.33%;
        }
        
        .skill-item {
            background: #eef2ff; 
            color: #1e3a8a;
            font-weight: 600; 
            padding: 4px 8px;
            border-radius: 8px; 
            font-size: 12px;
            display: inline-block;
            margin: 2px 4px 2px 0;
        }
        
        /* Professional summary paragraph */
        .summary-text {
            font-size: 14px;
            line-height: 1.6;
            text-align: justify;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="cv-container">
        <header class="header">
            <h1><?php echo htmlspecialchars($cv['full_name'] ?? 'Your Name'); ?></h1>
            <p class="role"><?php echo htmlspecialchars($cv['target_role'] ?? ''); ?></p>
            <div class="contact-info">
                <table>
                    <tr>
                        <?php $contact_items = []; ?>
                        <?php if (!empty($cv['email'])): $contact_items[] = '<a href="mailto:' . htmlspecialchars($cv['email']) . '"><i class="fas fa-envelope"></i> ' . htmlspecialchars($cv['email']) . '</a>'; endif; ?>
                        <?php if (!empty($cv['phone'])): $contact_items[] = '<i class="fas fa-phone"></i> ' . htmlspecialchars($cv['phone']); endif; ?>
                        <?php if (!empty($cv['address'])): $contact_items[] = '<i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($cv['address']); endif; ?>
                        <?php if (!empty($cv['linkedin_url'])): $contact_items[] = '<a href="' . htmlspecialchars($cv['linkedin_url']) . '"><i class="fab fa-linkedin"></i> LinkedIn</a>'; endif; ?> 
                        <?php if (!empty($cv['github_url'])): $contact_items[] = '<a href="' . htmlspecialchars($cv['github_url']) . '"><i class="fab fa-github"></i> GitHub</a>'; endif; ?>
                        
                        <?php 
                        $items_per_row = 3;
                        $chunks = array_chunk($contact_items, $items_per_row);
                        foreach($chunks as $chunk): ?>
                            <?php foreach($chunk as $item): ?>
                                <td><?php echo $item; ?></td>
                            <?php endforeach; ?>
                            <?php 
                            // Fill empty cells if needed
                            for($i = count($chunk); $i < $items_per_row; $i++): ?>
                                <td></td>
                            <?php endfor; ?>
                        <?php if($chunk !== end($chunks)): ?>
                            </tr><tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                </table>
            </div>
        </header>

        <?php if (!empty($cv['summary'])): ?>
        <section class="section">
            <h2>Professional Summary</h2>
            <p class="summary-text"><?php echo nl2br(htmlspecialchars($cv['summary'])); ?></p>
        </section>
        <?php endif; ?>

        <?php if ($experiences->rowCount() > 0): ?>
        <section class="section">
            <h2>Experience</h2>
            <?php while ($exp = $experiences->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="entry">
                <div class="entry-header">
                    <table>
                        <tr>
                            <td class="entry-title"><?php echo htmlspecialchars($exp['job_title']); ?></td>
                            <td class="entry-meta"><?php echo htmlspecialchars($exp['start_date']); ?> – <?php echo htmlspecialchars($exp['end_date'] ?: 'Present'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="entry-subtitle"><?php echo htmlspecialchars($exp['company_name']); ?><?php if(!empty($exp['location'])) echo " • " . htmlspecialchars($exp['location']); ?></div>
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

        <?php if ($educations->rowCount() > 0): ?>
        <section class="section">
            <h2>Education</h2>
            <?php while ($edu = $educations->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="entry">
                <div class="entry-header">
                    <table>
                        <tr>
                            <td class="entry-title"><?php echo htmlspecialchars($edu['degree']); ?></td>
                            <td class="entry-meta"><?php echo htmlspecialchars($edu['start_date']); ?> – <?php echo htmlspecialchars($edu['end_date']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="entry-subtitle"><?php echo htmlspecialchars($edu['institution']); ?></div>
            </div>
            <?php endwhile; ?>
        </section>
        <?php endif; ?>

        <?php if (!empty($projects) && $projects->rowCount() > 0): ?>
        <section class="section">
            <h2>Projects</h2>
            <?php while ($proj = $projects->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="entry">
                <div class="entry-header">
                    <table>
                        <tr>
                            <td class="entry-title"><?php echo htmlspecialchars($proj['project_name']); ?></td>
                            <?php if (!empty($proj['project_url'])): ?>
                                <td class="entry-meta"><a href="<?php echo htmlspecialchars($proj['project_url']); ?>">View Project</a></td>
                            <?php endif; ?>
                        </tr>
                    </table>
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

        <?php if (!empty($certificates) && $certificates->rowCount() > 0): ?>
        <section class="section">
            <h2>Licenses & Certificates</h2>
            <?php while ($cert = $certificates->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="entry">
                 <div class="entry-header">
                    <table>
                        <tr>
                            <td class="entry-title"><?php echo htmlspecialchars($cert['certificate_name']); ?></td>
                            <td class="entry-meta"><?php echo htmlspecialchars($cert['issue_date']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="entry-subtitle">
                    <?php echo htmlspecialchars($cert['issuing_organization']); ?>
                    <?php if (!empty($cert['credential_url'])): ?>
                         - <a href="<?php echo htmlspecialchars($cert['credential_url']); ?>">Show Credential</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </section>
        <?php endif; ?>

        <?php if ($skills->rowCount() > 0): ?>
        <section class="section">
            <h2>Skills</h2>
            <div class="skills-container">
                <table class="skills-table">
                    <tr>
                        <?php 
                        $all_skills = [];
                        while ($skill = $skills->fetch(PDO::FETCH_ASSOC)): 
                            $all_skills[] = $skill['skill_name'];
                        endwhile;
                        
                        $skills_chunks = array_chunk($all_skills, ceil(count($all_skills) / 3));
                        $max_rows = max(array_map('count', $skills_chunks));
                        
                        for($row = 0; $row < $max_rows; $row++): ?>
                            <?php for($col = 0; $col < 3; $col++): ?>
                                <td>
                                    <?php if(isset($skills_chunks[$col][$row])): ?>
                                        <span class="skill-item"><?php echo htmlspecialchars($skills_chunks[$col][$row]); ?></span>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                            <?php if($row < $max_rows - 1): ?></tr><tr><?php endif; ?>
                        <?php endfor; ?>
                    </tr>
                </table>
            </div>
        </section>
        <?php endif; ?>
    </div>
</body>
</html>