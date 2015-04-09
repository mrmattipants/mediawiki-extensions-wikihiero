/*global ve, OO */

/**
 * MediaWiki hieroglyphics inspector.
 *
 * @class
 * @extends ve.ui.MWLiveExtensionInspector
 *
 * @constructor
 * @param {Object} [config] Configuration options
 */
ve.ui.MWHieroInspector = function VeUiMWHieroInspector( config ) {
	// Parent constructor
	ve.ui.MWLiveExtensionInspector.call( this, config );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWHieroInspector, ve.ui.MWLiveExtensionInspector );

/* Static properties */

ve.ui.MWHieroInspector.static.name = 'hiero';

ve.ui.MWHieroInspector.static.icon = 'hiero';

ve.ui.MWHieroInspector.static.title =
	OO.ui.deferMsg( 'wikihiero-visualeditor-mwhieroinspector-title' );

ve.ui.MWHieroInspector.static.modelClasses = [ ve.dm.MWHieroNode ];

ve.ui.MWHieroInspector.static.dir = 'ltr';

/* Registration */

ve.ui.windowFactory.register( ve.ui.MWHieroInspector );
