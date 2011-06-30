<?php
/**
 * Import Multiple Images during Product Import
 *
 */
 
class Addonline_Catalog_Model_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product
{

   protected function  addMissingSlash($filename)  {
     if ($filename[0]!=DS) {
       $filename = DS . $filename;
     }
     return $filename;
   }

    /**
     * Save product (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        }
        else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        }
        else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

        	foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if ((!isset($importData[$field]) || $importData[$field]=='') && $attribute && $attribute->getIsRequired()) {
			        /**
					 * Force short_description = description if it's empty
					 */
                	if ($field=='short_description') {
                		$importData['short_description']=$importData['description'];
                	} else {
	                	$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
	                    Mage::throwException($message);
                	}
                }
            }
            
            /* ajout pour import des produits configurables */
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                if (!isset($importData["associated_products_sku"])) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', "associated_products_sku");
                    Mage::throwException($message);
                }
                if (!isset($importData["associated_products_attributes"])) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', "associated_products_attributes");
                    Mage::throwException($message);
                }
            } 
        }

        $this->setProductTypeInstance($product);

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                }
                catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }


        
        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (in_array($field, $this->_imageFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                }
                else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);

        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();

        // Permet d'utiliser la valeur de 'image' pour les champs 'small_image' et 'thumbnail' si ceux ci n'ont pas était remplis.
        $imageData = array();
        $notImportedImageField = array();
        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
            	if (!isset($imageData[$importData[$field]])) {
                    $imageData[$importData[$field]] = array();
                }
                $imageData[$importData[$field]][] = $field;
            } else {
				$notImportedImageField[]=$field;            	
            }
        }
        
        foreach ($imageData as $file => $fields) {
            $fields = array_merge($fields, $notImportedImageField);
            foreach ($fields as $field) {
				if (trim($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
					$arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $field);
                }
            }
        }
        
		/**
		 * Allows you to import multiple images for each product.
		 * Simply add a 'gallery' column to the import file, and separate
		 * each image with a semi-colon.
		 */
	        try {
	                $galleryData = explode(',',$importData["gallery"]);
	                foreach($galleryData as $gallery_img) {
				$file =  $this->addMissingSlash($gallery_img);
		                if (trim($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
		                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => null); // no "media_attribute" so that it isn't imported as thumbnail, base, or small
		                }
	                }
	        } catch (Exception $e) {}        
		/* End Modification */

	    $addedFilesCorrespondence =
            $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes($product, $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import', false, false);

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }

    	 /**
		 * Allows you to import configuarble product and to associate him with simple produtcs
		 */
	        try {
// 
// exemple de FORMAT JSON du champ configurable_products_data setté sur le produit configurable    
//{"60": [{"attribute_id": "80", "label": "Bleu", "value_index": "7"}], "62": [{"attribute_id": "80", "label": "Blanc", "value_index": "53"}]}
//
// exemple de FORMAT "PHP" du même champ configurable_products_data setté sur le produit configurable    
//Array (
//    [60] => Array (
//            [0] => Array (
//                    [attribute_id] => 901
//                    [label] => 12 mois 44 numéros + suppléments thématiques
//                    [value_index] => 45
//                )
//        )
//    [62] => Array (
//            [0] => Array (
//                    [attribute_id] => 901
//                    [label] => 6 mois 22 numéros + suppléments thématiques
//                    [value_index] => 46
//                )
//        )
//)

// idem pour le champ configurable_attribute_data
//Array (
//    [0] => Array
//        (
//            [id] => 
//            [label] => Durée d'abonnement
//            [use_default] => 
//            [position] => 
//            [values] => Array
//              (
//                    [0] => Array
//                        (
//                            [label] => 12 mois 44 numéros + suppléments thématiques
//                            [attribute_id] => 901
//                            [value_index] => 45
//                            [is_percent] => 0
//                            [pricing_value] => 
//                        )
//
//                    [1] => Array
//                        (
//                            [label] => 6 mois 22 numéros + suppléments thématiques
//                            [attribute_id] => 901
//                            [value_index] => 46
//                            [is_percent] => 0
//                            [pricing_value] => 
//                        )
//
//                )
//
//            [attribute_id] => 901
//            [attribute_code] => duree_abonnement
//            [frontend_label] => Durée d'abonnement
//            [store_label] => Durée d'abonnement
//            [html_id] => configurable__attribute_0
//        )
//
//)
	        	
	        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE 
	        		&& $importData["associated_products_sku"] 
	        		&& $importData["associated_products_attributes"]) {
	        	//Liste des codes produits "fils" du produit configurable :
	        	$associatedProductSkus = explode(',',$importData["associated_products_sku"]);
//				Mage::log('associatedProductSkus : ');
//				Mage::log($associatedProductSkus);
	        	//Liste des codes attributs à utiliser pour "construire" le produit configurable :
	        	$associatedProductAttributesCodes = explode(',',$importData["associated_products_attributes"]);
//				Mage::log('associatedProductAttributesCodes : ');
//				Mage::log($associatedProductAttributesCodes);

	        	$configurableProductsData = array();
			    $configurableAttributesData = array();
			    $configurableAttributesIds = array(); 
			    $configurableAttributesSource = array();

				//liste de attributs existants sur le produit (selon son attribute set...)
			    $attributes = $product->getTypeInstance(true)->getSetAttributes($product);
			    foreach ($attributes as $attribute) {
			    	//on recherche les codes attributs de "associated_products_attributes" dans les attributs du produits
			    	//pour trouver les données de ces attributs
			    	if ($product->getTypeInstance(true)->canUseAttribute($attribute, $product)
			    		 && in_array($attribute->getAttributeCode(), $associatedProductAttributesCodes)) {
						//$position : la position du codeAttribut dans  "associated_products_attributes" est utilisée pour 
						// définir la position dans la liste déroulante des options
			    		$position = array_search($attribute->getAttributeCode(), $associatedProductAttributesCodes);
						//$configurableAttributesSource : objet Source qui permet de récupérer les libellés des différentes options (valeurs) de l'attribut 
			    		$configurableAttributesSource[$attribute->getAttributeCode()] = $attribute->getSource();
						//$configurableAttributesIds : liste des identifiant d'attributs 
			    		$configurableAttributesIds[] = $attribute->getAttributeId();
						
			    		//$idAttributeData : utile seulement si on importe un produit déjà existant en base,
			    		// si c'est un nouveau produit sa valeur sera nulle
			    		// sinon il faut récupérer sa valeur dans getConfigurableAttributesAsArray sous peine de doublonner les attributs ...
			    		$idAttributeData = null;
						$productConfigurableAttributes  = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
					    if (is_array($productConfigurableAttributes)) {
					    	foreach ($productConfigurableAttributes as $productAttributeData) {
					    		if ($productAttributeData["attribute_id"] == $attribute->getAttributeId()) {
					    			$idAttributeData = $productAttributeData["id"];
					    		}
					    	}
					    }  

						//$configurableAttributesData : données relatives à l'attribut configurable, est setté sur le $product à la fin
					    $configurableAttributesData[$attribute->getAttributeCode()]= array("id"=>$idAttributeData,
											"label"=>$attribute->getFrontend()->getLabel(),
											"use_default"=>null,
											"position"=>$position,
											"values"=>array(),
											"attribute_id"=>$attribute->getAttributeId(), 
											"attribute_code"=>$attribute->getAttributeCode(), 
											"frontend_label"=>$attribute->getFrontend()->getLabel(), 
											"store_label"=>$attribute->getStoreLabel(), 
											"html_id"=>"configurable__attribute_".$position);
			    	}
			    }
	                
	            foreach($associatedProductSkus as $sku)
	            {
	            	//on recherche chaque produit configuré "fils" (skus dans "associated_products_sku")
	            	$associatedProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
	                if ($associatedProduct->getId()) {
	                	//on "construit" $configurableProductsData : donnée relatives aux produits configurés, sera setté sur le $product à la fin
	                	$configurableProductsData[$associatedProduct->getId()] = array(); 
	                	foreach ($associatedProductAttributesCodes as $attributeCode) {
	                		//pour chaque attribut "de configuration", on récupère la valeur de l'attribut sur le produit "configuré" fils
	                		$attributeValue = $associatedProduct->getData($attributeCode);
//	                		Mage::log('product id '.$associatedProduct->getId().' attribute_code '.$attributeCode.' value : '.$attributeValue);
	                		$attributeData = $configurableAttributesData[$attributeCode];
	                		$configurableProductsData[$associatedProduct->getId()][]=
									array("attribute_id"=>$attributeData["attribute_id"], 
											"label"=>$configurableAttributesSource[$attributeCode]->getOptionText($attributeValue), 
											"value_index"=>$attributeValue);

							
							//à chaque fois qu'on trouve une nouvelle valeur de l'attribut, 
							//on ajoute un item dans "values" de $configurableAttributesData 
							$trouvee=false;
							foreach ($attributeData["values"] as $attributeDataValue) {
								if ($attributeDataValue["value_index"] == $attributeValue) {
									$trouvee=true;
								}
							}
							if (!$trouvee) {
								$configurableAttributesData[$attributeCode]["values"][] = 
										array("label"=>$configurableAttributesSource[$attributeCode]->getOptionText($attributeValue), 
											"attribute_id"=>$attributeData["attribute_id"], 
											"value_index"=>$attributeValue,
											"is_percent"=>0,
											"pricing_value"=>null);		
							}
							
	                	}	                		                	
	                }
	            }
	                
                $product->getTypeInstance()->setUsedProductAttributeIds($configurableAttributesIds);

//				Mage::log($configurableProductsData);
                $product->setConfigurableProductsData($configurableProductsData);
	                
//				Mage::log(array_values($configurableAttributesData));
                $product->setConfigurableAttributesData(array_values($configurableAttributesData));
			        
                $product->setCanSaveConfigurableAttributes(true);
   
                $product->save();

	    	}   
	    } catch (Exception $e) {}        
		/* End Modification */  
    	        
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->save();


    
        return true;
    }

}
