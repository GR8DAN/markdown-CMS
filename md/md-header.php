<div class="row">
    <div class="one column">
        <?php
            echo "<a href=\"{$MD_SETTINGS['HOME']}\">";
            echo "<img title=\"{$MD_SETTINGS['SITE_NAME']}\" alt=\"{$MD_SETTINGS['SITE_NAME']} Logo\" src=\"{$MD_SETTINGS['SITE_LOGO']}\" />";
            echo "</a>";
        ?>
    </div>
    <div class="two columns">
        <?php
            echo "<h1 class=\"sitetitle\">{$MD_SETTINGS['SITE_NAME']}</h1>";
        ?>
    </div>
    <div class="six columns">
        <?php
            //Check for info.top file
            echo Md_ProcessText($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$MD_SETTINGS['INFO_TOP_FILE'],$parsedown);
        ?>
    </div>
    <div class="three columns">
        <?php
            //Check for search.txt code file
            echo Md_ProcessText($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$MD_SETTINGS['SITE_SEARCH'],$parsedown);
        ?>
    </div>
</div>
<div class="row">
    <div class="twelve columns">
        <?php
            Md_ProcessMenu($parsedown);
        ?>
    </div>
</div>