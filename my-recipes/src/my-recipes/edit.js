/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */


export default function Edit(props) {
    const today = new Date().toISOString().split("T")[0];
	function handleheadingchange(e){
		props.setAttributes({headingtext: e.target.value })
	}
	function handleDateChange(e) {
		props.setAttributes({ recipedate: e.target.value });
	}
    function handleCuisineChange(e){
		props.setAttributes({ cuisine: e.target.value });
	}
	function handleIngredientsChange(e){
		props.setAttributes({ ingredients: e.target.value });
	}
	return (
		<div {...useBlockProps()} className="recipeediter">
           <h4><label htmlFor="title">Block Title : </label>
		   <input id="title" onChange={handleheadingchange} type='text' value={props.attributes.headingtext}/></h4>
		   <div style={{ marginTop: '20px' }}>
				<label htmlFor="recipe-date">Recipe Date:</label>
				<input
					id="recipe-date"
					type="date"
					value={props.attributes.recipedate}
					onChange={handleDateChange}
					 max={today}
				/>
			</div>
			<div style={{ marginTop: '20px' }}>
				<label htmlFor="recipe-cuisine">Cuisine:</label>
				<input
					id="recipe-cuisine"
					type="text"
					placeholder="e.g. Italian, Mexican"
					value={props.attributes.cuisine}
					onChange={handleCuisineChange}
				/>
			</div>

			<div style={{ marginTop: '20px' }}>
				<label htmlFor="recipe-ingredients">Ingredients:</label>
				<input
					id="recipe-ingredients"
					type="text"
					placeholder="e.g. chicken, garlic, tomato"
					value={props.attributes.ingredients}
					onChange={handleIngredientsChange}
				/>
				<div style={{ marginTop: '20px' }}>
    <label htmlFor="recipe-type">Type:</label>
    <input
        id="recipe-type"
        type="text"
        placeholder="e.g. main course, dessert, salad"
        value={props.attributes.type}
        onChange={(e) => props.setAttributes({ type: e.target.value })}
    />
</div>
			</div>
		</div>
	);
}
