/**
 * Validate HTML form
 * 
 * Validate HTML forms. iterates over all form fields, checking for elements,
 * which do have the class "required" assigned. If one of those elements does
 * not have a value, the element is marked with the "errnous" class and an
 * error message is given to the user.
 *
 * The method will return false if one element does not validate, so it can be
 * embedded into forms like:
 * <code>
 *  <form onsubmit="return validateForm( this );">
 * </code>
 *
 * @param form $form  
 * @return bool
 */
function validateForm( form )
{
    // Get a list with all input elements in current form
    var elements = form.elements;
    var valid    = true;
    var errors   = [];
    for ( var i = 0; i < elements.length; ++i )
    {
        // Check if element is required and does not contain anything
        if ( elements[i].hasAttribute( 'class' ) &&
             ( elements[i].getAttribute( 'class' ).indexOf( 'required' ) >= 0 ) &&
             ( !elements[i].value ) )
        {
            // Assign additional CSS class for a visual indication of error
            elements[i].setAttribute(
                'class',
                elements[i].getAttribute( 'class' ) + " errnous"
            );
            valid    = false;

            // Append an error to teh lsit of error messages
            errors.push( "Form element " + elements[i].getAttribute( 'name' ) + " is required, but has no value assigned." );
        }
    }

    // Alert error messages, of form did not validate.
    if ( !valid )
    {
        alert( errors.join( "\n" ) );
    }

    return valid;
}
