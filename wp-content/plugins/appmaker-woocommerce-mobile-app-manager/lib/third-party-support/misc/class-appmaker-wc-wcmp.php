<?php
// Fixed issue of removing tax_query for WCMp plugin
global $WCMp;
remove_filter('pre_get_posts', array( &$WCMp->product, 'convert_business_id_to_taxonomy_term_in_query') );
