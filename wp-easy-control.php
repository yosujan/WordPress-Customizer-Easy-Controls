<?php 
 /**
 * This is a library for creating controls in customizer of your wordpress theme
 * Author: Sujan Devkota
 * Github: yosujan
 */


if ( ! class_exists( 'WP_Customize_Control' ) )
  return NULL;



/**
*
* Class WP_Easy_Control 
*
*/
class WP_Easy_Control extends WP_Customize_Control {

	public $type = 'easy-control';

	public $sub_fields;

/**
*
* Constructor
*
*/
	public function __construct( $manager, $id, $args = array() ) {

	if($args['sub_fields']) $this->sub_fields=$args['sub_fields'];

	parent::__construct( $manager, $id, $args );

	}



/**
*
* Enquene Necessary Javascript and CSS 
*
*/
	public function enqueue() {

	    wp_enqueue_style( 'easy-control', get_template_directory_uri().'/css/easy-control.css', array(), '1.0.0');

	    wp_enqueue_script( 'easy-control-js', get_template_directory_uri() . '/js/easy-control.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker' ), '1.0.0',  true);

	  }



/**
*
* Render Content Function
*
*/

	public function render_content() {

		$value = $this->value();

		$array_value= json_decode($value ,true);

		if(is_array($array_value)){

			/* render nothing if value is empty */

		}
		else{

		  $array_value=$this->create_default_data_array();

		  $array_value=json_decode($array_value, true);

		}
		?>
		 <input
		 type="hidden"
		    <?php $this->input_attrs(); ?>
		    <?php if ( ! isset( $this->input_attrs['value'] ) ) : ?>
		    value="<?php echo esc_attr( $this->value() ); ?>"
		    <?php endif; ?>
		    <?php $this->link(); ?>
		    class="main-value-input"
		    id="main-value-<?php echo $this->id; ?>"
		    />

		<div class="repeater-wrapper">

		<ul id="ul-<?php echo $this->id; ?>" class="sortable-list easy-control">

		<?php
		$count=1;

		foreach($array_value as $args) { ?>

		  <li class="all-sub-container" id="val-<?php echo $count++;?>">

		    <div class="custom-control-heading"><?php echo $this->label; ?></div>

		    <div class="custom-control-body" style="display: none;">
		      
		      <?php 

		      $this->render_dropdown($args['mainValue']);
		      
		      $datacount=0;

		      foreach($this->sub_fields as $sub_field) {

		        $this->render_sub_fields( $sub_field, $args['sub_fields'][$datacount][$sub_field['name']] );

		        $datacount++;

		      }
		      
		      ?>

		      <button type="button" class="delete">Delete</button>
		    </div>

		  </li>

		<?php } ?>

		</ul>

		<button type="button" class="add-new">Add New</button>

		<div> 

		<?php

	}


/**
 * Check the structure of the customizer data and create a empty array and return json
 *
 * @return json
 */
	protected function create_default_data_array(){

	  $default_data=array();

	  $default_data=array("mainValue" => 0);
	  
	  $sub_fields=array();
	  
	  foreach($this->sub_fields as $sub_field){
	    
	    $data=array( $sub_field['name'] => $sub_field['value'] );

	    $sub_fields[]=$data;

	  }

	  $default_data['sub_fields']=$sub_fields;

	  $return_array[0]=$default_data;

	  return json_encode($return_array);

	}


/**
* Render Dropdown Input Types
*
*/
	protected function render_dropdown($args){

		$dropdown_name = $this->id;

		$show_option_none = __( '&mdash; Select &mdash;' );

		$option_none_value = '0';

		$dropdown = wp_dropdown_pages(
		  array(
		      'name'              => $dropdown_name,
		      'class'             =>'main-value',
		      'echo'              => 0,
		      'show_option_none'  => $show_option_none,
		      'option_none_value' => $option_none_value,
		      'selected'          => $args,
		  )
		);

		if ( empty( $dropdown ) ) {

		  $dropdown = sprintf( '<select id="%1$s" name="%1$s">', esc_attr( $dropdown_name ) );

		  $dropdown .= sprintf( '<option value="%1$s">%2$s</option>', esc_attr( $option_none_value ), esc_html( $show_option_none ) );

		  $dropdown .= '</select>';

		}


		$search= 'value="'. $args.'"';

		$replace= 'value="'.$args.'" selected';

		$dropdown = str_replace($search, $replace, $dropdown);

		echo $dropdown;

	}


/**
* Render Different Input Fields
*
*/

	protected function render_sub_fields($args, $value) {
      
		$input_id = $args['id'];

		$description = (isset($args['description'])?$args['description']:"");

		$options= (isset($args['options'])?$args['options']:array());

		$label = (isset($args['label'])?$args['label']:"");

		$description_id = '_customize-description-' . $input_id;

		$describedby_attr = ( ! empty( $description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';

		switch ( $args['type'] ) {


			case 'checkbox':

				$sub_field_value = $value; 
				?>
				<span class="customize-inside-control-row">

					<label>

						<input
						id="<?php echo $this->id.'-'.esc_attr( $input_id ); ?>"
						type="checkbox"
						name="<?php echo $args['name']; ?>"
						value="<?php echo esc_attr( $sub_field_value ); ?>"
						<?php checked( $sub_field_value ); ?>
						/>

						<?php echo esc_html( $label ); ?>

					</label>

					<?php if ( ! empty( $args['description'] ) ) : ?>

						<span class="description customize-control-description"><?php echo $description; ?></span>

					<?php endif; ?>
				</span>
				<?php break;

			case 'radio':

				$sub_field_value = $value; //isset( $data[$args['name']]) ? $data[$args['name']] : $args['value']; 

				if ( empty( $options ) ) return;
				
				if ( ! empty( $label ) ): ?>

					<span class="customize-control-title"><?php echo esc_html( $label ); ?></span>

				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>

					<span class="description customize-control-description"><?php echo $description ; ?></span>

				<?php endif; ?>

				<?php 

				$radio_count=rand(00,999999);

				foreach ( $options as $ovalue => $label ) : ?>

					<label>

						<span class="customize-inside-control-row">

							<input
							type="radio"
							class="<?php echo $args['name']; ?>" 
							name="<?php echo $args['name'].'-'.$radio_count.'-'.$sub_field_value; ?>"
							value="<?php echo esc_attr( $ovalue ); ?>"
							<?php checked( $sub_field_value, $ovalue ); ?>
							/>
							<?php echo esc_html( $label ); ?>

						</span>
					</label>

				<?php endforeach; ?>

				<?php break;

		case 'select':

			$sub_field_value = $value; // isset( $data[$args['name']]) ? $data[$args['name']] : $args['value']; 

			if ( empty( $options ) )  return; ?>

			<?php if ( ! empty( $label ) ) : ?>

				<label for="<?php echo esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo esc_html( $label ); ?></label>

			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>

				<span class="description customize-control-description"><?php echo $description; ?></span>

			<?php endif; ?>

			<select id="<?php echo $this->id.'-'.esc_attr( $input_id ); ?>" name="<?php echo $args['name']; ?>" <?php echo $describedby_attr; ?> >

			<?php
			foreach ( $options as $ovalue => $label ) {

				echo '<option value="' . esc_attr( $ovalue ) . '"' . selected( $sub_field_value, $ovalue, false ) . '>' . $label . '</option>';

			}
			?>
			</select>

		<?php break; 

			case 'textarea':

			$sub_field_value = $value; //isset( $data[$args['name']]) ? $data[$args['name']] : $args['value']; 

			?>
			<?php if ( ! empty( $label ) ) : ?>

				<label class="customize-control-title"><?php echo esc_html( $label ); ?></label>

			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>

				<span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $description; ?></span>

			<?php endif; ?>

			<textarea
			id="<?php echo $this->id.'-'.esc_attr( $input_id ); ?>"
			name="<?php echo $args['name']; ?>"
			rows="5"
			<?php echo $describedby_attr; ?> ><?php echo esc_textarea( $sub_field_value ); ?></textarea>

		<?php break;

		default:

			$sub_field_value = $value; //isset( $data[$args['name']]) ? $data[$args['name']] : $args['value']; 

			?>

			<?php if ( ! empty( $label ) ) : ?>
				<label for="<?php echo esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo esc_html( $label ); ?></label>
			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>

				<span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $description; ?></span>

			<?php endif; ?>

			<input
			id="<?php echo $this->id.'-'.esc_attr( $input_id ); ?>"
			name="<?php echo $args['name']; ?>"
			<?php echo $describedby_attr; ?>
			value="<?php echo esc_attr( $sub_field_value ); ?>"
			/>

		<?php break;

		} //end switch statement 


	}


}
