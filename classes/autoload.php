<?php
/**
 * arbit autoload file
 *
 * This file is part of arbit.
 *
 * arbit is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * arbit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with arbit; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/*
 * This array is autogenerated and topoligically sorted. Do not change anything
 * in here, but just run the following script in the trunk/ directory.
 *
 * # scripts/gen_autoload_files.php
 */
return array(
    'arbitBackendCouchDBGroupDocument'                   => 'classes/backend/couchdb/document/group.php',
    'arbitBackendCouchDbProjectDocument'                 => 'classes/backend/couchdb/document/project.php',
    'arbitBackendCouchDbUserDocument'                    => 'classes/backend/couchdb/document/user.php',
    'arbitBackendCouchDbGroupView'                       => 'classes/backend/couchdb/view/group.php',
    'arbitBackendCouchDbUserView'                        => 'classes/backend/couchdb/view/user.php',
    'arbitTranslateable'                                 => 'classes/framework/translateable.php',
    'arbitException'                                     => 'classes/framework/exception.php',
    'arbitBackendIniException'                           => 'classes/backend/ini/exception.php',
    'arbitBackendNoSuchProjectException'                 => 'classes/backend/ini/exception.php',
    'arbitBackendNoSuchModuleException'                  => 'classes/backend/ini/exception.php',
    'arbitBackendUnknownConfigurationException'          => 'classes/backend/ini/exception.php',
    'arbitBackendIniConfigurationBase'                   => 'classes/backend/ini/base.php',
    'arbitBackendIniMainConfiguration'                   => 'classes/backend/ini/main.php',
    'arbitBackendIniConfigurationManager'                => 'classes/backend/ini/manager.php',
    'arbitBackendIniModuleConfiguration'                 => 'classes/backend/ini/module.php',
    'arbitBackendIniProjectConfiguration'                => 'classes/backend/ini/project.php',
    'arbitController'                                    => 'classes/controller/base.php',
    'arbitAdminController'                               => 'classes/controller/admin.php',
    'arbitAdminCouchdbController'                        => 'classes/controller/admin/couchdb.php',
    'arbitCoreModuleController'                          => 'classes/controller/core.php',
    'arbitMainControllerSlots'                           => 'classes/controller/core/slots.php',
    'arbitCacheable'                                     => 'classes/framework/cacheable.php',
    'arbitBaseStruct'                                    => 'classes/framework/struct.php',
    'arbitSignalSlotStruct'                              => 'classes/framework/signal_struct.php',
    'arbitUserSignalStruct'                              => 'classes/controller/core/structs/user.php',
    'arbitCoreConfirmedUserStruct'                       => 'classes/controller/core/structs/confirmed_user.php',
    'arbitCoreDashboardInfoStruct'                       => 'classes/controller/core/structs/dashboard_info.php',
    'arbitCoreLoginUserStruct'                           => 'classes/controller/core/structs/login_user.php',
    'arbitCoreLogoutUserStruct'                          => 'classes/controller/core/structs/logout_user.php',
    'arbitCoreNewGroupStruct'                            => 'classes/controller/core/structs/new_group.php',
    'arbitCoreNewUserStruct'                             => 'classes/controller/core/structs/new_user.php',
    'arbitCoreProjectComponentsUpdateStruct'             => 'classes/controller/core/structs/project_components_update.php',
    'arbitCoreProjectVersionsUpdateStruct'               => 'classes/controller/core/structs/project_version_update.php',
    'arbitCoreModuleUserAuthentification'                => 'classes/controller/core/user/authentification.php',
    'arbitCoreModuleUserOpenIDAuthentification'          => 'classes/controller/core/user/openid.php',
    'arbitCoreModuleUserOpenIDAuthentificationFilter'    => 'classes/controller/core/user/openid_filter.php',
    'arbitCoreModuleUserPasswordAuthentification'        => 'classes/controller/core/user/password.php',
    'arbitCronController'                                => 'classes/controller/cron.php',
    'arbitErrorController'                               => 'classes/controller/error.php',
    'arbitControllerException'                           => 'classes/controller/exception.php',
    'arbitExceptionNotFoundMarker'                       => 'classes/framework/exception_marker.php',
    'arbitControllerUnknownActionException'              => 'classes/controller/exception.php',
    'arbitMainController'                                => 'classes/controller/main.php',
    'arbitProjectController'                             => 'classes/controller/project.php',
    'arbitResult'                                        => 'classes/controller/result.php',
    'arbitRedirectResult'                                => 'classes/controller/redirect.php',
    'arbitDispatcherConfiguration'                       => 'classes/dispatcher.php',
    'arbitCouchDbFacadeBase'                             => 'classes/facade/couchdb/base.php',
    'arbitGroupFacade'                                   => 'classes/facade/base/group.php',
    'arbitCouchDbGroupFacade'                            => 'classes/facade/couchdb/group.php',
    'arbitFacadeProjectManager'                          => 'classes/facade/base/manager.php',
    'arbitCouchDbFacadeProjectManager'                   => 'classes/facade/couchdb/manager.php',
    'arbitProjectFacade'                                 => 'classes/facade/base/project.php',
    'arbitCouchDbProjectFacade'                          => 'classes/facade/couchdb/project.php',
    'arbitUserFacade'                                    => 'classes/facade/base/user.php',
    'arbitCouchDbUserFacade'                             => 'classes/facade/couchdb/user.php',
    'arbitFacadeException'                               => 'classes/facade/exception.php',
    'arbitFacadeUnknownClassException'                   => 'classes/facade/exception.php',
    'arbitFacadeUnknownProjectException'                 => 'classes/facade/exception.php',
    'arbitFacadeExistsException'                         => 'classes/facade/exception.php',
    'arbitExceptionConflictMarker'                       => 'classes/framework/exception_marker.php',
    'arbitFacadeUserExistsException'                     => 'classes/facade/exception.php',
    'arbitFacadeGroupExistsException'                    => 'classes/facade/exception.php',
    'arbitFacadeProjectExistsException'                  => 'classes/facade/exception.php',
    'arbitModelNotPersistantException'                   => 'classes/facade/exception.php',
    'arbitFacadeNotFoundException'                       => 'classes/facade/exception.php',
    'arbitFacadeManager'                                 => 'classes/facade/manager.php',
    'arbitFrameworkBase'                                 => 'classes/framework/base.php',
    'arbitCache'                                         => 'classes/framework/cache.php',
    'arbitFilesystemCache'                               => 'classes/framework/cache/filesystem.php',
    'arbitCacheRegistry'                                 => 'classes/framework/cache_registry.php',
    'arbitFrameworkCliTool'                              => 'classes/framework/cli_tool.php',
    'arbitFrameworkActionCliTool'                        => 'classes/framework/cli_tool/action.php',
    'arbitFrameworkAdminCliTool'                         => 'classes/framework/cli_tool/admin.php',
    'arbitFrameworkCouchdbCliTool'                       => 'classes/framework/cli_tool/couchdb.php',
    'arbitFrameworkCronCliTool'                          => 'classes/framework/cli_tool/cron.php',
    'arbitCliLogger'                                     => 'classes/framework/cli_tool/logger.php',
    'arbitCliResponseWriter'                             => 'classes/framework/cli_tool/writer.php',
    'arbitScheduledTaskCommand'                          => 'classes/framework/command.php',
    'arbitHasModuleCommand'                              => 'classes/framework/command/has_module.php',
    'arbitFrameworkDiff'                                 => 'classes/framework/diff.php',
    'arbitFrameworkLineDiff'                             => 'classes/framework/diff/line.php',
    'arbitFrameworkTextDiff'                             => 'classes/framework/diff/text.php',
    'arbitFrameworkDiffToken'                            => 'classes/framework/diff/token.php',
    'arbitRuntimeException'                              => 'classes/framework/exception.php',
    'arbitPropertyException'                             => 'classes/framework/exception.php',
    'arbitPropertyValidationException'                   => 'classes/framework/exception.php',
    'arbitPropertyReadOnlyException'                     => 'classes/framework/exception.php',
    'arbitInvalidSignalException'                        => 'classes/framework/exception.php',
    'arbitInvalidSignalNameException'                    => 'classes/framework/exception.php',
    'arbitInvalidSignalDataException'                    => 'classes/framework/exception.php',
    'arbitHeadersSentException'                          => 'classes/framework/exception.php',
    'arbitSessionTakeOverException'                      => 'classes/framework/exception.php',
    'arbitNoSuchCacheException'                          => 'classes/framework/exception.php',
    'arbitItemNotCacheableException'                     => 'classes/framework/exception.php',
    'arbitFileUploadErrorException'                      => 'classes/framework/exception.php',
    'arbitPhpErrorException'                             => 'classes/framework/exception.php',
    'arbitCommitParserException'                         => 'classes/framework/exception.php',
    'arbitHttpInputConverter'                            => 'classes/framework/http/input.php',
    'arbitHttpArrayConverter'                            => 'classes/framework/http/array.php',
    'arbitHttpNumberConverter'                           => 'classes/framework/http/number.php',
    'arbitHttpNoConverter'                               => 'classes/framework/http/raw.php',
    'arbitHttpStringConverter'                           => 'classes/framework/http/string.php',
    'arbitHttpTools'                                     => 'classes/framework/http_tools.php',
    'arbitDateTimeFormatter'                             => 'classes/framework/i18n/datetime.php',
    'arbitUnknownLocaleException'                        => 'classes/framework/i18n/exception.php',
    'arbitUnknownDateFormatException'                    => 'classes/framework/i18n/exception.php',
    'arbitUnknownPatternTokenException'                  => 'classes/framework/i18n/exception.php',
    'arbitLogger'                                        => 'classes/framework/logger.php',
    'arbitMessenger'                                     => 'classes/framework/messenger.php',
    'arbitMessengerBase'                                 => 'classes/framework/messenger/base.php',
    'arbitMailMessenger'                                 => 'classes/framework/messenger/mail.php',
    'arbitMailMessengerTransportBase'                    => 'classes/framework/messenger/mail/transport_base.php',
    'arbitMailMessengerMtaTransport'                     => 'classes/framework/messenger/mail/mta_transport.php',
    'arbitFrameworkMimeTypeGuesser'                      => 'classes/framework/mime_type.php',
    'arbitCommitMessageParser'                           => 'classes/framework/misc/commit_parser.php',
    'arbitSession'                                       => 'classes/framework/session.php',
    'arbitSessionBackend'                                => 'classes/framework/session/backend.php',
    'arbitHttpSessionBackend'                            => 'classes/framework/session/http_backend.php',
    'arbitMemorySessionBackend'                          => 'classes/framework/session/memory_backend.php',
    'arbitSignalSlot'                                    => 'classes/framework/signal_slot.php',
    'arbitTranslationManager'                            => 'classes/framework/translation.php',
    'arbitCachingTranslationContext'                     => 'classes/framework/translation_context.php',
    'arbitUnviewableSignalSlotStruct'                    => 'classes/framework/unviable_signal_struct.php',
    'arbitLineChart'                                     => 'classes/model/chart/line.php',
    'arbitChartPalette'                                  => 'classes/model/chart/palette.php',
    'arbitPieChart'                                      => 'classes/model/chart/pie.php',
    'arbitPqiPieChart'                                   => 'classes/model/chart/pie/pqi.php',
    'arbitModelException'                                => 'classes/model/exception.php',
    'arbitModelVersionNotFoundException'                 => 'classes/model/exception.php',
    'arbitModelBase'                                     => 'classes/model/base.php',
    'arbitModelGroup'                                    => 'classes/model/group.php',
    'arbitModelProject'                                  => 'classes/model/project.php',
    'arbitModelUser'                                     => 'classes/model/user.php',
    'arbitModelValidatorBase'                            => 'classes/model/validator/base.php',
    'arbitModelArrayValidator'                           => 'classes/model/validator/array.php',
    'arbitModelDummyValidator'                           => 'classes/model/validator/dummy.php',
    'arbitModelIntegerValidator'                         => 'classes/model/validator/integer.php',
    'arbitModelObjectValidator'                          => 'classes/model/validator/object.php',
    'arbitModelSetValidator'                             => 'classes/model/validator/set.php',
    'arbitModelStringValidator'                          => 'classes/model/validator/string.php',
    'arbitModuleDefintion'                               => 'classes/module/definition.php',
    'arbitCoreDefinition'                                => 'classes/module/core.php',
    'arbitModuleException'                               => 'classes/module/exception.php',
    'arbitModuleFileNotFoundException'                   => 'classes/module/exception.php',
    'arbitModuleDefinitionNotFoundException'             => 'classes/module/exception.php',
    'arbitUnknownModuleException'                        => 'classes/module/exception.php',
    'arbitModuleDefinitionLocator'                       => 'classes/module/locator.php',
    'arbitModuleManager'                                 => 'classes/module/manager.php',
    'arbitRequest'                                       => 'classes/request/base.php',
    'arbitCliRequest'                                    => 'classes/request/cli.php',
    'arbitHttpRequest'                                   => 'classes/request/http.php',
    'arbitHttpRequestParser'                             => 'classes/request/parser.php',
    'arbitRoute'                                         => 'classes/router/arbit_route.php',
    'arbitRouterException'                               => 'classes/router/exception.php',
    'arbitUnknownControllerException'                    => 'classes/router/exception.php',
    'arbitHttpRouter'                                    => 'classes/router/http.php',
    'arbitViewModelDecorationDependencyInjectionManager' => 'classes/view/decorator_manager.php',
    'arbitViewException'                                 => 'classes/view/exception.php',
    'arbitViewNoDecoratorsExceptions'                    => 'classes/view/exception.php',
    'arbitViewDecorationFailedException'                 => 'classes/view/exception.php',
    'arbitViewHandler'                                   => 'classes/view/handler.php',
    'arbitTemplateViewHandler'                           => 'classes/view/handler/template.php',
    'arbitViewEmailHandler'                              => 'classes/view/handler/email.php',
    'arbitViewTemplateFunctions'                         => 'classes/view/handler/functions.php',
    'arbitTemplateRecursiveIteratorIterator'             => 'classes/view/handler/functions/recursive_iterator.php',
    'arbitViewHttpBinaryHandler'                         => 'classes/view/handler/http_binary.php',
    'arbitViewJsonHandler'                               => 'classes/view/handler/json.php',
    'arbitModuleTemplateViewHandler'                     => 'classes/view/handler/module_template.php',
    'arbitViewTextHandler'                               => 'classes/view/handler/text.php',
    'arbitViewTextTemplateFunctions'                     => 'classes/view/handler/text/functions.php',
    'arbitViewXHtmlHandler'                              => 'classes/view/handler/xhtml.php',
    'arbitViewXHtmlTemplateFunctions'                    => 'classes/view/handler/xhtml/functions.php',
    'arbitViewXmlHandler'                                => 'classes/view/handler/xml.php',
    'arbitViewManager'                                   => 'classes/view/manager.php',
    'arbitDecorateable'                                  => 'classes/framework/decorateable.php',
    'arbitViewModel'                                     => 'classes/view/model.php',
    'arbitViewGroupModel'                                => 'classes/view/model/business/group.php',
    'arbitViewProjectModel'                              => 'classes/view/model/business/project.php',
    'arbitViewUserModel'                                 => 'classes/view/model/business/user.php',
    'arbitViewCliContextModel'                           => 'classes/view/model/context/cli.php',
    'arbitViewDashboardContextModel'                     => 'classes/view/model/context/dashboard.php',
    'arbitViewErrorContextModel'                         => 'classes/view/model/context/exception.php',
    'arbitViewErrorNotFoundContextModel'                 => 'classes/view/model/context/not_found.php',
    'arbitViewProjectContextModel'                       => 'classes/view/model/context/project.php',
    'arbitViewCoreModel'                                 => 'classes/view/model/core/base.php',
    'arbitViewCoreAboutModel'                            => 'classes/view/model/core/about.php',
    'arbitViewCoreCacheListModel'                        => 'classes/view/model/core/cache_list.php',
    'arbitViewCoreDummyModel'                            => 'classes/view/model/core/dummy.php',
    'arbitViewCorePermissionsModel'                      => 'classes/view/model/core/permissions.php',
    'arbitViewCoreProjectModel'                          => 'classes/view/model/core/project.php',
    'arbitViewCoreUserModel'                             => 'classes/view/model/core/user.php',
    'arbitViewCoreUserRegistrationModel'                 => 'classes/view/model/core/user/registration.php',
    'arbitViewCoreUserLoginModel'                        => 'classes/view/model/core/user/login.php',
    'arbitViewCoreUserRegisteredModel'                   => 'classes/view/model/core/user/registered.php',
    'arbitViewCoreUserAcceptModel'                       => 'classes/view/model/core/user_accept.php',
    'arbitViewCoreUserAccountModel'                      => 'classes/view/model/core/user_account.php',
    'arbitViewDashboardProjectModel'                     => 'classes/view/model/dashboard_project.php',
    'arbitViewDataModel'                                 => 'classes/view/model/data.php',
    'arbitViewModuleModel'                               => 'classes/view/model/module.php',
    'arbitViewProjectConfigurationModel'                 => 'classes/view/model/project_configuration.php',
    'arbitViewSearchResultModel'                         => 'classes/view/model/search_result.php',
    'arbitViewSearchResultDocumentModel'                 => 'classes/view/model/search_result_document.php',
    'arbitSignalSlotViewStruct'                          => 'classes/view/model/signal_struct.php',
    'arbitCoreProjectComponentsUpdateViewStruct'         => 'classes/view/model/signal_struct/components_update.php',
    'arbitCoreConfirmedUserViewStruct'                   => 'classes/view/model/signal_struct/confirmed_user.php',
    'arbitCoreDashboardInfoViewStruct'                   => 'classes/view/model/signal_struct/dashboard_info.php',
    'arbitCoreLoginUserViewStruct'                       => 'classes/view/model/signal_struct/login_user.php',
    'arbitCoreLogoutUserViewStruct'                      => 'classes/view/model/signal_struct/logout_user.php',
    'arbitCoreNewGroupViewStruct'                        => 'classes/view/model/signal_struct/new_group.php',
    'arbitCoreNewUserViewStruct'                         => 'classes/view/model/signal_struct/new_user.php',
    'arbitCoreProjectVersionsUpdateViewStruct'           => 'classes/view/model/signal_struct/version_update.php',
    'arbitViewUserMessageModel'                          => 'classes/view/model/error.php',
    'arbitViewUserSuccessModel'                          => 'classes/view/model/success.php',
);

