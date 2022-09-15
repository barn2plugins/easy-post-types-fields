/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

/**
 * Handles the display of the welcome step of the wizard.
 */
const WelcomeStep = ( { goToNextStep, goToPreviousStep, nextButtonLabel } ) => {

    return (
        <div style={ { textAlign: 'center', padding: '1rem 0' } }>
            <Button
				isPrimary
				onClick={ () => goToNextStep() }
            >
				{ __( 'Create a custom post type' ) }
            </Button>
        </div>
    )

}

export default WelcomeStep