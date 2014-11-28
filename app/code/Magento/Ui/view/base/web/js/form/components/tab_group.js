/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
define([
    './collapsible',
    'Magento_Ui/js/lib/spinner'
], function(Collapsible, loader) {
    'use strict';
   
    var __super__ = Collapsible.prototype;

    return Collapsible.extend({

        /**
         * Invokes initElement method of parent class, calls 'initActivation' method
         * passing element to it.
         * @param {Object} elem
         * @returns {Object} - reference to instance
         */
        initElement: function(elem){
            __super__.initElement.apply(this, arguments);    

            this.initActivation(elem)
                .hideLoader();

            return this;
        },

        /**
         * Binds 'onValidate' method as handler for data storage's 'validate' event
         * 
         * @return {Object} - reference to instance
         */
        initListeners: function(){
            var data    = this.provider.data,
                handler = this.onValidate.bind(this);

            __super__.initListeners.apply(this, arguments); 

            data.on('validate', handler, this.name);
            
            return this;
        },

        /**
         * Activates element if one is first or if one has 'active' propert
         * set to true.
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        initActivation: function(elem){
            var elems   = this.elems(),
                isFirst = !elems.indexOf(elem);

            if(isFirst || elem.active()){
                elem.activate();
            }

            return this;
        },

        hideLoader: function () {
            loader.get(this.name).hide();
        },

        /**
         * Delegates 'validate' method on element, then reads 'invalid' property
         * of params storage, and if defined, activates element, sets 
         * 'allValid' property of instance to false and sets invalid's
         * 'focused' property to true.
         * @param {Object} elem
         */
        validate: function(elem){
            var params  = this.provider.params,
                result  = elem.delegate('validate'),
                invalid = false;

            _.some(result, function(item){
                return !item.valid && (invalid = item.target);
            });

            if (invalid && !params.get('invalid')) {
                params.set('invalid', invalid);

                elem.activate();
                invalid.focused(true);
            }
        },

        /**
         * Sets 'allValid' property of instance to true, then calls 'validate' method
         * of instance for each element 
         */
        onValidate: function(){
            var elems;

            elems = this.elems.sortBy(function(elem){
                return !elem.active();
            });            

            elems.each(this.validate, this);
        }
    });
});