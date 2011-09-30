//Author:       Ben Nadel
//Copyright Ben Nadel @ KinkySolutions.com 2006
function GetParentNodeWithTagName( objNode, strTagName ){
    // Lowercase the tag name for comparison.
    strTagName = strTagName.toLowerCase();

    // Crawl up the parent node chain. Keep crawling until we find the
    // node with the proper tag name, we hit a null node, or we hit
    // a non-text node that has not tag name (the document object).
    for (
        objNode = objNode.parentNode ;
        (
            objNode && (
                (objNode.tagName && (objNode.tagName.toLowerCase() != strTagName)) ||
                (!objNode.tagName && (objNode.nodeType != 3))
            )
        );
        objNode = objNode.parentNode
        ){
        // Nothing has to be done within in the FOR loop. We are purely
        // using the FOR loop to crawl up the DOM structure.
    }

    // Return the node. At this point, it might contains a valide
    // parent node, or it might be null.
    return( objNode );
}

//Author:       Ben Nadel
//Copyright Ben Nadel @ KinkySolutions.com 2006
// This is our testing method.
function FindParent( objNode, strTagName ){
    var objParent = GetParentNodeWithTagName( objNode, strTagName );

    // Check to see if we found the parent.
    if (objParent != null){
        return objParent.getAttribute( "id" );
    } else {
        return;
    }
}
