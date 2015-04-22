<?php
namespace Craft;

class Headings_HeadingsFieldType extends BaseFieldType
{
    /**
     * Fieldtype name
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Headings');
    }
    
	/**
	 * Returns the default values
	 *
	 * @return array
	 */
	public function getHeadingsValueDefaults()
	{
		return array(
			'text' => false
		);
	}

    /**
     * Define database column
     *
     * @return AttributeType::String
     */
    public function defineContentAttribute()
    {
        return array(AttributeType::Mixed);
    }
    
	/**
	 * Defines the settings.
	 *
	 * @access protected
	 * @return array
	 */
	protected function defineSettings()
	{
		$settings['text'] = AttributeType::Bool;	
		return $settings;
	}

	/**
	 * Returns the field's settings HTML.
	 *
	 * @return string|null
	 */
	public function getSettingsHtml()
	{		
		return craft()->templates->render('headings/_fieldtype/settings', array(
			'settings'             		=> $this->getSettings()
		));
	}

    /**
     * Display our fieldtype
     *
     * @param string $name  Our fieldtype handle
     * @return string Return our fields input template
     */
    public function getInputHtml($name, $value)
    {
    	// Settings
    	$settings = $this->getSettings();
    	
    	// Linkit Types
	
    	// Setup Entry Field
		$entryElementType = craft()->elements->getElementType(ElementType::Entry);
		
		if(is_array($value))
		{
			if(!($value['entryCriteria'] instanceof ElementCriteriaModel))
			{
				$value['entryCriteria'] = craft()->elements->getCriteria(ElementType::Entry);
				$value['entryCriteria']->id = false;
			}
		}
		else
		{
			$defaultEntryCriteria = craft()->elements->getCriteria(ElementType::Entry);
			$defaultEntryCriteria->id = false;
		}
		
		$entrySelectionCriteria = array();
		$entrySelectionCriteria['localeEnabled'] = null;	
		$entrySelectionCriteria['status'] = null;

    	
		    	
       	// Include Javascript & CSS
		craft()->templates->includeJsResource('lib/fileupload/jquery.ui.widget.js');
		craft()->templates->includeJsResource('lib/fileupload/jquery.fileupload.js');
    	craft()->templates->includeJsResource('headings/js/headings.js');
    	craft()->templates->includeCssResource('headings/css/headings.css');
    	
		// Render Field
    	return craft()->templates->render('headings/_fieldtype/input', array(
            'name'  => $name,
            'value' => $value,
            'settings' => $settings      
        ));        
    }
    
	/**
	 * Returns the input value as it should be saved to the database.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function prepValueFromPost($value)
	{
		return json_encode($value);
	}

	/**
	 * Preps the field value for use.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function prepValue($value)
	{
		
		if(is_array($value))
		{
			// Get Defualts
			$defaults = $this->getHeadingsValueDefaults();
		
			// Merge With Defaults
			$value = array_merge($defaults, $value);
			
			
			// Process?
			
				$entryCriteria = craft()->elements->getCriteria(ElementType::Entry);
				if($value['entry'] && $value['type'] == 'entry')
				{
					if(is_array($value['entry']))
					{
						$entryCriteria->id = array_values(array_filter($value['entry']));
					}
					else
					{
						$entryCriteria->id = false;
					}
				}
				else
				{
					$entryCriteria->id = false;
				}
				$value['entryCriteria'] = $entryCriteria;


			
		}				
		
		return $value;
		
	}    
	
		
	/**
	 * Validates the value beyond the checks that were assumed based on the content attribute.
	 *
	 * Returns 'true' or any custom validation errors.
	 *
	 * @param array $value
	 * @return true|string|array
	 */
	public function validate($value)
	{
		$errors = array();
		
		$defaults = $this->getHeadingsValueDefaults();
	
		if(is_array($value))
		{
			// Merge With Defaults
			$value = array_merge($defaults, $value);
		}

		if ($errors)
		{
			return $errors;
		}
		else
		{
			return true;
		}	
	}		
	
}
