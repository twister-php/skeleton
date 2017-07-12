<?php

/**
 *	Example
 */
return function( Container $c )
{
	header('Content-Type: text/plain');
?>
User-agent: *
Allow: /
Disallow: /admin/

Sitemap: http://<?php echo $c->request->uri->authority; ?>/sitemap.xml

<?php
};
