<?php
/**
 * 
 * Markdown engine rules for wiki markup.
 * 
 * @category Solar
 * 
 * @package Solar_Markdown
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */

/**
 * Markdown parse/render engine.
 */
Solar::loadClass('Solar_Markdown');

/**
 * 
 * Markdown engine rules for wiki markup.
 * 
 * This class implements a plugin set for the Markdown-Extra syntax;
 * be sure to visit the [Markdown-Extra][] site for syntax examples.
 * 
 * [Markdown-Extra]: http://www.michelf.com/projects/php-markdown/extra/
 * 
 * @category Solar
 * 
 * @package Solar_Markdown
 * 
 * @todo Implement the markdown-in-html portion of Markdown-Extra.
 * 
 */
class Solar_Markdown_Wiki extends Solar_Markdown {
    
    /**
     * 
     * User-defined configuration values.
     * 
     * This sets the plugins and their processing order for the engine.
     * 
     * @var array
     * 
     */
    protected $_Solar_Markdown_Wiki = array(
        'plugins' => array(
            
            // highest-priority prepare and cleanup
            'Solar_Markdown_Wiki_Filter',
            
            // for Markdown images and links
            'Solar_Markdown_Plugin_StripLinkDefs',
            
            // blocks
            'Solar_Markdown_Wiki_MethodSynopsis',
            'Solar_Markdown_Wiki_Header',
            'Solar_Markdown_Extra_Table',
            'Solar_Markdown_Plugin_HorizRule',
            'Solar_Markdown_Plugin_List',
            'Solar_Markdown_Extra_DefList',
            'Solar_Markdown_Wiki_ColorCodeBlock',
            'Solar_Markdown_Plugin_CodeBlock',
            'Solar_Markdown_Plugin_BlockQuote',
            'Solar_Markdown_Wiki_Paragraph',
            
            // spans
            'Solar_Markdown_Plugin_CodeSpan',
            'Solar_Markdown_Wiki_Link',
            'Solar_Markdown_Plugin_Image',
            'Solar_Markdown_Plugin_Link',
            'Solar_Markdown_Plugin_Uri',
            'Solar_Markdown_Plugin_Encode',
            'Solar_Markdown_Wiki_EmStrong',
            'Solar_Markdown_Plugin_Break',
        ),
    );
}
?>