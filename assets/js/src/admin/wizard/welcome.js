/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Button, Card, CardFooter, Popover, __experimentalText as Text, __experimentalHeading as Heading } from '@wordpress/components';
import { Component } from '@wordpress/element';
import { Icon, info } from '@wordpress/icons';
import { isEmpty, has, map } from 'lodash';
 
/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { SetupWizardSettings } from '../../../../../vendor/barn2/setup-wizard/resources/js/utilities';
import { HeadingField, ImageField, ListField } from '../../../../../vendor/barn2/setup-wizard/resources/js/fields';

class Welcome extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			isStepDescriptionPopoverVisible: false,
			activeElement: null,
			loading: false,
		};
	}

	/**
	 * Set the loading state.
	 *
	 * @param {boolean} loading
	 * @returns {void}
	 */
	setLoading = ( loading ) =>
		this.setState( ( state ) => ( {
			loading: loading,
		} ) );

	/**
	 * Toggle popover
	 */
	togglePopover() {
		this.setState( ( prevState ) => ( {
			isStepDescriptionPopoverVisible: ! prevState.isStepDescriptionPopoverVisible,
		} ) );
	}

	render() {
		const { step } = this.props;
		const { isStepDescriptionPopoverVisible, loading } = this.state;

		const descriptionClassNames = classNames( {
			has_tooltip: ! isEmpty( step.tooltip ),
		} );

		const fieldComponents = {
			heading: HeadingField,
			title: HeadingField,
			image: ImageField,
			list: ListField,
		};

		return (
			<div className="woocommerce-profile-wizard__store-details">
				<div className="woocommerce-profile-wizard__step-header">
					<Text
						variant="title.small"
						as="h2"
						size="20"
						lineHeight="28px"
					>
						{ isEmpty( step.heading )
							? sprintf(
									__( 'Welcome to %s', '' ),
									SetupWizardSettings.plugin_name
							  )
							: step.heading }
					</Text>

					<Text
						variant="body"
						as="p"
						className={ descriptionClassNames }
					>
						{ ! isEmpty( step.description ) && (
							<>{ step.description }</>
						) }
						{ ! isEmpty( step.tooltip ) && (
							<>
								{ isStepDescriptionPopoverVisible ? (
									<Button
										isTertiary
										label={ step.tooltip }
										className="tooltip-btn"
										disabled
										onClick={ () => {
											this.togglePopover();
										} }
									>
										<Icon icon={ info } />
									</Button>
								) : (
									<Button
										isTertiary
										label={ step.tooltip }
										className="tooltip-btn"
										onClick={ () => {
											this.togglePopover();
										} }
									>
										<Icon icon={ info } />
									</Button>
								) }
							</>
						) }
					</Text>
					{ ! isEmpty( step.tooltip ) &&
						isStepDescriptionPopoverVisible && (
							<Popover
								focusOnMount="container"
								position="bottom center"
								onClose={ () => {
									setTimeout( () => {
										this.togglePopover();
									}, 100 );
								} }
							>
								{ step.tooltip }
							</Popover>
						) }
				</div>
				<Card isBorderless style={ { backgroundColor: '#f6f7f7' } }>
					{ map( step.fields, function ( field, field_key ) {
						if ( ! fieldComponents.hasOwnProperty( field.type ) ) {
							return;
						}

						const type = field.type;

						const fieldClasses =
							has( field, 'classes' ) &&
							! isEmpty( field.classes )
								? field.classes
								: [];

						const cardClasses = classNames(
							{
								'checkbox-wrapper': type === 'checkbox',
							},
							fieldClasses
						);

						const FieldComponent = fieldComponents[ field.type ];

						return (
							<FieldComponent
								key={ field_key }
								field={ field }
								fieldKey={ field_key }
								value={ '' }
								isSubmitting={ false }
								cardClasses={ cardClasses }
							/>
						);
					} ) }

					<CardFooter
						style={ { backgroundColor: '#f6f7f7' } }
						justify="center"
					>
						<Button
							isPrimary
							isBusy={ loading }
							disabled={ loading }
							onClick={ () => this.props.goToNextStep() }
						>
							{ __( 'Create a custom post type' ) }
						</Button>
					</CardFooter>
				</Card>
			</div>
		);
	}
}

export default Welcome;
