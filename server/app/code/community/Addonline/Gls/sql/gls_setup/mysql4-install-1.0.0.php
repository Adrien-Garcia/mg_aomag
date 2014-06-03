<?php
/**
 * Copyright (c) 2014 GLS
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category    Addonline
 * @package     Addonline_Gls
 * @copyright   Copyright (c) 2014 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 * @license    http://www.opensource.org/licenses/MIT  The MIT License (MIT)
 **/

$installer = $this;

$installer->startSetup();

$this->addAttribute('order', 'gls_relay_point_id', array(
		'type'     => 'varchar',
		'label'    => 'Id du point relay GLS',
		'visible'  => true,
		'required' => false,
		'input'    => 'text',
));

$this->addAttribute('order', 'gls_warn_by_phone', array(
		'type'     => 'varchar',
		'label'    => 'Prévenir par téléphone',
		'visible'  => true,
		'required' => false,
		'input'    => 'text',
));

$this->addAttribute('order', 'gls_trackid', array(
		'type'     => 'varchar',
		'label'    => 'Trackid',
		'visible'  => true,
		'required' => false,
		'input'    => 'text',
));

$installer->endSetup();