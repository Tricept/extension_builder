{namespace k=EBT\ExtensionBuilder\ViewHelpers}
/**
 *<f:if condition="{classSchema}">
 * {classSchema.description}</f:if>
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *<f:for each="{classSchema.annotations}" as="annotation">
 * @{annotation}</f:for>
 */