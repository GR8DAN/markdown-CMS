<footer>
    <hr/>
    <div class="row">
        <?php
            /* For each footer.x.md file in the site root a div is created into
            ** into which the text from that file is converted to HTML and inserted.
            ** x must be a single number between 0 and 9, lowest added first.
            */
            $footer_count=0;
            $footer_content=array();
            foreach (glob($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."footer.[0-9].md") as $footer_filename) {
                $footer_content[$footer_count] = file_get_contents($footer_filename);
                $footer_count++;
            }
            if($footer_count==0) {
                /* No footer files provided, use default */
                $footer_filename="md-footer.txt";
                echo file_get_contents($footer_filename);
            } else {
                /* How may footer columns ($footer_count) contains number of footer files found */
                /* convert to footer classes */
                $footer_class = (int) 12 / $footer_count;
                $div_class=OneToTenInEnglish($footer_class);
                foreach ($footer_content as $key=>$val ){
                    $footer_text=$parsedown->text($val);
                    echo "<div class=\"".$div_class." columns\">{$footer_text}</div>";
                }
            }
        ?>
    </div>
    <hr/>
</footer>