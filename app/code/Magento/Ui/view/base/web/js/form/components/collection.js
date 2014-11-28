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
    'mage/utils',
    'Magento_Ui/js/form/component',
    'underscore',
    'Magento_Ui/js/lib/registry/registry'
], function (utils, Component, _, registry) {
    'use strict';

    var __super__ = Component.prototype;

    var defaults = {
        lastIndex: 0,
        template: 'ui/form/components/collection'
    };

    var childTemplate = {
        template:   "{name}.{itemTemplate}",
        parent:     "{name}",
        name:       "{childIndex}",
        dataScope:  "{childIndex}"
    };

    return Component.extend({

        /**
         * Extends instance with default config, calls initialize of parent
         * class, calls initChildren method.
         */
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initChildren();
        },

        /**
         * Activates the incoming child and triggers the update event.
         *
         * @param {Object} elem - Incoming child.
         */
        initElement: function (elem) {
            __super__.initElement.apply(this, arguments);

            elem.activate();

            this.trigger('update');
        },

        /**
         * Loops over corresponding data in data storage,
         * creates child for each and pushes it's identifier to initialItems array.
         *
         * @returns {Collection} Chainable.
         */
        initChildren: function () {
            var data     = this.provider.data,
                children = data.get(this.dataScope),
                initial  = this.initialItems = [];
                        
            _.each(children, function(item, index){
                initial.push(index);
                this.addChild(index);
            }, this);

            return this;
        },

        /**
         * Creates new item of collection, based on incoming 'index'.
         * If not passed creates one with 'new_' prefix.
         *
         * @param {String|Object} [index] - Index of a child.
         * @returns {Collection} Chainable.
         */
        addChild: function(index) {
            this.childIndex = !_.isString(index) ?
                ('new_' + this.lastIndex++) :
                index;

            this.renderer.render({
                layout: [
                    utils.template(childTemplate, this)
                ]
            });

            return this;
        },

        /**
         * Returnes true if current set of items differ from initial one,
         * or if some child has been changed.
         *
         * @returns {Boolean}
         */
        hasChanged: function(){
            var initial = this.initialItems,
                current = this.elems.pluck('index'),
                changed = !utils.identical(initial, current);

            return changed || this.elems.some(function(elem){
                return _.some(elem.delegate('hasChanged'));
            });
        },

        /**
         * Initiates validation of its' children components.
         *
         * @returns {Array} An array of validation results.
         */
        validate: function(){
            var elems;

            this.allValid = true;

            elems = this.elems.sortBy(function(elem){
                return !elem.active();
            });

            elems = elems.map(this._validate, this);

            return _.flatten(elems);
        },

        /**
         * Iterator function for components validation.
         * Activates first invalid child component.
         *
         * @param {Object} elem - Element to run validation on.
         * @returns {Array} An array of validation results.
         */
        _validate: function(elem){
            var result = elem.delegate('validate'),
                invalid;

            invalid = _.some(result, function(item){
                return !item.valid;
            });

            if(this.allValid && invalid){
                this.allValid = false;

                elem.activate();
            }

            return result;  
        },
        
        /**
         * Creates function that removes element
         * from collection using '_removeChild' method.
         * @param  {Object} elem - Element that should be removed.
         * @returns {Function}
         *      Since this method is used by 'click' binding,
         *      it requires function to invoke.
         */
        removeChild: function(elem) {
            return function() {
                var confirmed = confirm(this.removeMessage);

                if (confirmed) {
                    this._removeChild(elem);
                }

            }.bind(this);
        },

        /**
         * Removes elememt from both collection and data storage,
         * activates first element if removed one was active,
         * triggers 'update' event.
         *
         * @param {Object} elem - Element to remove.
         */
        _removeChild: function(elem) {
            var isActive = elem.active(),
                first;

            elem.destroy();

            first = this.elems.first();

            if (first && isActive) {
                first.activate();
            }

            this.trigger('update');
        }
    });
});

