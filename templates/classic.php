<?php if (empty($is_pdf)): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CV: <?php echo htmlspecialchars($cv['full_name'] ?? 'Untitled'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600&display=swap" rel="stylesheet">
<?php endif; ?>

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
        color:black;
        <?php if (!empty($is_pdf)): ?>
            padding: 20px;
            margin: 0;
            box-shadow: none;
            width: 100%;
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
        border-bottom: 2px solid #1a1a1a;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    
    .header h1 {
        font-size: 32px;
        margin: 0;
        font-weight: 600;
        letter-spacing: -0.5px;
    }
    
    .contact-info {
        margin-top: 12px;
        font-size: 12px;
        color: #100f0f;
        <?php if (!empty($is_pdf)): ?>
            line-height: 1.4;
        <?php endif; ?>
    }
    
    <?php if (!empty($is_pdf)): ?>
    .contact-info {
        display: block;
    }
    
    .contact-info span {
        display: inline-block;
        margin-right: 15px;
        position: relative;
    }
    
    .contact-info span:not(:last-child)::after {
        content: "•";
        position: absolute;
        right: -10px;
        /* color: #888; */
    }
    <?php else: ?>
    .contact-info span:not(:last-child)::after {
        content: "•";
        margin: 0 0.5em;
        /* color: #888; */
    }
    <?php endif; ?>
    
    .section { 
        margin-bottom: <?php echo !empty($is_pdf) ? '20px' : '1.6rem'; ?>;
        page-break-inside: avoid;
    }
    
    .section h2 {
        font-size: 18px;
        font-weight: 700;
        text-transform: uppercase;
        border-bottom: 1px solid #ccc;
        padding-bottom: <?php echo !empty($is_pdf) ? '3px' : '0.2em'; ?>;
        margin-bottom: <?php echo !empty($is_pdf) ? '12px' : '0.8rem'; ?>;
        letter-spacing: 0.5px;
    }
    
    .entry { 
        margin-bottom: <?php echo !empty($is_pdf) ? '15px' : '1rem'; ?>;
        page-break-inside: avoid;
    }
    
    .entry-header {
        <?php if (!empty($is_pdf)): ?>
            display: block;
            margin-bottom: 5px;
        <?php else: ?>
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
        <?php endif; ?>
        font-size: <?php echo !empty($is_pdf) ? '10px' : '0.95em'; ?>;
    }

    .entry-header table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .entry-title { 
        font-weight: 600;
        font-size: 16px;
        <?php if (!empty($is_pdf)): ?>
            display: inline-block;
            margin-right: 20px;
        <?php endif; ?>
    }
    
    .entry-meta { 
        font-size: 12px;
        text-align: right;
    }
    
    .entry-subtitle { 
        font-weight: 600; 
        margin-bottom: <?php echo !empty($is_pdf) ? '5px' : '0.3rem'; ?>;
        color: #333030ff;
        font-size: 14px;
        clear: both;
    }

    .entry li {
    margin-bottom: 3px;
    font-size: 14px;
    line-height: 1.4;
}
    
    .skills-list {
        <?php if (!empty($is_pdf)): ?>
            list-style: disc;
            padding-left: 16px;
            margin: 0;
            columns: 2;
            column-gap: 30px;
        <?php else: ?>
            columns: 2;
            list-style: disc;
            padding-left: 1.2em;
        <?php endif; ?>
    }
    
    .skills-list li { 
        margin-bottom: <?php echo !empty($is_pdf) ? '4px' : '0.3rem'; ?>;
        font-weight: 500;
        font-size: 14px;
        break-inside: avoid;
    }
    
    /* Professional summary styling */
    .summary-text {
        font-size: 14px;
        line-height: 1.7;
        /* color: #333; */
        text-align: justify;
        margin: 0;
    }
    
    <?php if (!empty($is_pdf)): ?>
    /* PDF-specific fixes */
    .clearfix::after {
        content: "";
        display: table;
        clear: both;
    }
    
    /* Ensure proper page breaks */
    .section {
        orphans: 3;
        widows: 3;
    }
    
    .entry {
        orphans: 2;
        widows: 2;
    }
    
    /* Better spacing for PDF */
    .section:last-child {
        margin-bottom: 0;
    }
    <?php endif; ?>
</style>

<?php if (empty($is_pdf)): ?>
</head>
<body>
<?php endif; ?>

<div class="cv-container">
    <header class="header">
        <h1><?php echo htmlspecialchars($cv['full_name'] ?? 'Your Name'); ?></h1>
        <div class="contact-info">
            <?php if (!empty($cv['phone'])): ?>
                <span><?php echo htmlspecialchars($cv['phone']); ?></span>
            <?php endif; ?>
            <?php if (!empty($cv['email'])): ?>
                <span><?php echo htmlspecialchars($cv['email']); ?></span>
            <?php endif; ?>
            <?php if (!empty($cv['address'])): ?>
                <span><?php echo htmlspecialchars($cv['address']); ?></span>
            <?php endif; ?>
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

<?php if (empty($is_pdf)): ?>
</body>
</html>
<?php endif; ?>