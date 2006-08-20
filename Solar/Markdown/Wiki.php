<?php
Solar::loadClass('Solar_Markdown');
class Solar_Markdown_Wiki extends Solar_Markdown {
    
    // we want to disallow HTML from almost everything
    protected $_Solar_Markdown_Wiki = array(
        'plugins' => array(
            
            // pre-processing on the source as a whole
            'Solar_Markdown_Plugin_Prefilter',
            'Solar_Markdown_Plugin_StripLinkDefs',
            
            // blocks
            'Solar_Markdown_Wiki_MethodSynopsis',
            'Solar_Markdown_Wiki_Header',
            'Solar_Markdown_Extra_Table',
            'Solar_Markdown_Plugin_HorizRule',
            'Solar_Markdown_Plugin_List',
            'Solar_Markdown_Extra_DefList',
            'Solar_Markdown_Plugin_CodeBlock',
            'Solar_Markdown_Plugin_BlockQuote',
            'Solar_Markdown_Wiki_Html',
            'Solar_Markdown_Plugin_Paragraph',
            
            // spans
            'Solar_Markdown_Plugin_CodeSpan',
            'Solar_Markdown_Wiki_Link',
            'Solar_Markdown_Plugin_Image',
            'Solar_Markdown_Plugin_Link',
            'Solar_Markdown_Plugin_Uri',
            'Solar_Markdown_Plugin_Encode',
            'Solar_Markdown_Extra_EmStrong',
            'Solar_Markdown_Plugin_Break',
        ),
    );
}
?>