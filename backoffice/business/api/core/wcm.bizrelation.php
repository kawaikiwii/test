<?php
/**
 * Project:     WCM
 * File:        biz_object.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmBizrelation class inherits from the {@link wcmObjectrelation} class
 * and represents business objects.
 * This class can be customized for client needs
 */
class wcmBizrelation extends wcmObjectrelation
{
	// Datas for new media attached to content
	public $media_text;
	public $media_description;
	public $media_link;
}

?>