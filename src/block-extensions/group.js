import { __experimentalToggleGroupControl as ToggleGroupControl, __experimentalToggleGroupControlOption as ToggleGroupControlOption, ToggleControl } from "@wordpress/components";

const { InspectorControls } = wp.blockEditor;
const { PanelBody } = wp.components;
const { createHigherOrderComponent } = wp.compose;
const { Fragment } = wp.element;
const { __ } = wp.i18n;

// Add new attributes for group link
const addExtraAttributes = (settings, name) => {
   if (name === "core/group" || name === "core/cover") {
      settings.attributes = {
         ...settings.attributes,
         hiddenContent: {
            type: "boolean",
            default: false,
         },
      };
   }
   return settings;
};

wp.hooks.addFilter("blocks.registerBlockType", "extend-core-blocks/logged-in-content", addExtraAttributes);

const withExtraControls = createHigherOrderComponent((BlockEdit) => {
   return (props) => {
      const { attributes, setAttributes, name } = props;
      if (name === "core/group") {
         const { hiddenContent } = attributes;
         return (
            <Fragment>
               <InspectorControls group="settings">
                  <PanelBody title={__("Hidden Content", "wtcgbg")} initialOpen={true}>
                     <ToggleControl
                        label={__("Only show this content for logged in users.", "wtcgbg")}
                        isBlock
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        checked={hiddenContent}
                        onChange={(newValue) => {
                           setAttributes({ hiddenContent: newValue });
                        }}
                     />
                  </PanelBody>
               </InspectorControls>
               <BlockEdit {...props} />
            </Fragment>
         );
      }
      return <BlockEdit {...props} />;
   };
}, "withExtraControls");

wp.hooks.addFilter("editor.BlockEdit", "extend-core-blocks/logged-in-content", withExtraControls);
 
function addAccessibilityRoleToImageBlocks(props, blockType, attributes) {
   const { name } = blockType;
   const { hiddenContent } = attributes;

   if (name === "core/group") {
      return hiddenContent ? { ...props, hiddenContent: "true" } : { ...props };
   }
   return props;
}

wp.hooks.addFilter("blocks.getSaveContent.extraProps", "extend-core-blocks/logged-in-content", addAccessibilityRoleToImageBlocks);
