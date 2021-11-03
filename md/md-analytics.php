<?php //Google analytics support
if(array_key_exists('TRACKING-ID',$MD_SETTINGS))
 echo "<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src='https://www.googletagmanager.com/gtag/js?id=".$MD_SETTINGS['TRACKING-ID']."'></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '".$MD_SETTINGS['TRACKING-ID']."');
</script>\n";
?>
