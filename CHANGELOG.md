# Changelog

## [0.12.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.11.1...reusables-laravel-v0.12.0) (2026-11-26)


### Features

* **media:** add dimension limits to ValidImage ([576b339](https://github.com/umaraziz0/reusables-laravel/commit/576b33907e8c03d9f0ec21a6eec0dab6916d756c)), closes [#140](https://github.com/umaraziz0/reusables-laravel/issues/140)
* **media:** add dimension limits to ValidImage ([#152](https://github.com/umaraziz0/reusables-laravel/issues/152)) ([cec7b9d](https://github.com/umaraziz0/reusables-laravel/commit/cec7b9df82b8026dd0fad95736d6e0eef1d16b1b))
* **media:** keep avif as an allowed image type ([e015629](https://github.com/umaraziz0/reusables-laravel/commit/e015629146e1dbdeae6bcebb2c30407d2e49e707))
* **queue:** fork Horizon workers with queue-prefork ([665c89d](https://github.com/umaraziz0/reusables-laravel/commit/665c89d170845910728df7cdbe38f1c617dc2cc0)), closes [#126](https://github.com/umaraziz0/reusables-laravel/issues/126)
* **queue:** fork Horizon workers with queue-prefork ([#149](https://github.com/umaraziz0/reusables-laravel/issues/149)) ([6ba2b88](https://github.com/umaraziz0/reusables-laravel/commit/6ba2b8861bb63ae9a3608c3f1939992e2e0ed320)), closes [#126](https://github.com/umaraziz0/reusables-laravel/issues/126)
* **queue:** split Horizon supervisors and make queues toggleable ([#151](https://github.com/umaraziz0/reusables-laravel/issues/151)) ([ac070d0](https://github.com/umaraziz0/reusables-laravel/commit/ac070d0822752cfd5c82373a8ec4d58444fea3a4)), closes [#148](https://github.com/umaraziz0/reusables-laravel/issues/148)

## [0.11.1](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.11.0...reusables-laravel-v0.11.1) (2026-11-18)


### Bug Fixes

* **vue-query:** register `QueryClient` - fixes devtools error ([335460a](https://github.com/umaraziz0/reusables-laravel/commit/335460a649ebfcc78b3d0ff097f462c598372ad6))
* **vue-query:** register `QueryClient` - fixes devtools error ([5902c11](https://github.com/umaraziz0/reusables-laravel/commit/5902c11aaf9b2b1b17fbece476696217b488d48c))
* **vue-query:** register QueryClient so dev pages render ([#137](https://github.com/umaraziz0/reusables-laravel/issues/137)) ([335460a](https://github.com/umaraziz0/reusables-laravel/commit/335460a649ebfcc78b3d0ff097f462c598372ad6))

## [0.11.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.10.0...reusables-laravel-v0.11.0) (2026-11-18)


### Features

* **components:** exclude component previews in production ([d0dbf4e](https://github.com/umaraziz0/reusables-laravel/commit/d0dbf4e3f290f5e1335febb2976cb0f5e9a1dce3))
* **components:** leave preview pages out of the production build ([f6c6149](https://github.com/umaraziz0/reusables-laravel/commit/f6c61494f92f6b3079af25c1e7ecb5ab92620743)), closes [#125](https://github.com/umaraziz0/reusables-laravel/issues/125)
* **components:** return 404 for component previews in production ([5956568](https://github.com/umaraziz0/reusables-laravel/commit/595656851ec6826c6860b4d566b47e4cb51500de))


### Bug Fixes

* **components:** drop the extra wrapper on three preview pages ([329137a](https://github.com/umaraziz0/reusables-laravel/commit/329137a74034193133e1a6c92b6ca904401485d1))
* **components:** keep the preview header on one line on mobile ([1fcb7a5](https://github.com/umaraziz0/reusables-laravel/commit/1fcb7a5a9ee2800f9e4934c899bed902cb3e0c3b))
* **components:** make preview pages consistent and fix the mobile header ([5dcb0c6](https://github.com/umaraziz0/reusables-laravel/commit/5dcb0c69049e93a0d77df23f796d988dcb71a510))
* **components:** match preview titles to their sidebar names ([1cb878b](https://github.com/umaraziz0/reusables-laravel/commit/1cb878b621c58694a1b3abc837bbe4f0693aa462))
* **components:** restore collapsible trigger styling in the preview ([fdcdd40](https://github.com/umaraziz0/reusables-laravel/commit/fdcdd402eb8391a5ac45662e8cea80a0bea3ca87))
* **components:** use the shared h3 section label on form previews ([cc40c9f](https://github.com/umaraziz0/reusables-laravel/commit/cc40c9ff05a4d3426b6725f89c590e7215040037))

## [0.10.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.9.0...reusables-laravel-v0.10.0) (2026-11-18)


### Features

* **frontend:** add Vite Doctor to frontend checks ([#117](https://github.com/umaraziz0/reusables-laravel/issues/117)) ([99efdce](https://github.com/umaraziz0/reusables-laravel/commit/99efdce93114fa72fbb45aaa39690f7a2019a0d9))


### Bug Fixes

* **boost:** keep the `inertia-vue` guideline as Markdown ([62b994f](https://github.com/umaraziz0/reusables-laravel/commit/62b994f2c0ac727bc4d536f247ce91717094c8a9))
* **components:** fix the Vue findings `vite-doctor` reports ([b3a59d8](https://github.com/umaraziz0/reusables-laravel/commit/b3a59d88a60ce76b8d8a7ad0b2ee98c8e7e9d214))
* **users:** escape LIKE wildcards in user search on SQLite ([8785d31](https://github.com/umaraziz0/reusables-laravel/commit/8785d318ebed0f0bcdafb9b6c361eba952052978))
* **users:** escape LIKE wildcards in user search on SQLite ([6e253a4](https://github.com/umaraziz0/reusables-laravel/commit/6e253a41ae6c8498d28c3d59b085494e066d2fba))

## [0.9.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.8.0...reusables-laravel-v0.9.0) (2026-11-16)


### Features

* **components:** add `Collapsible`, `Pagination` and `Table` to the components sidebar ([10e073d](https://github.com/umaraziz0/reusables-laravel/commit/10e073d93329fd2b1c7bff8c9cabfe4c3b1710f8))
* **components:** add `Pagination` showcase page ([5b8600d](https://github.com/umaraziz0/reusables-laravel/commit/5b8600ddca135fd450297c85bcda2d961fc8f766))
* **components:** add `Table` and `Pagination` showcase pages ([67d6186](https://github.com/umaraziz0/reusables-laravel/commit/67d6186793b94c1ba61acd5107d2529808d1cd54))
* **components:** add `Table` showcase page with server-side pagination ([6ccfa2a](https://github.com/umaraziz0/reusables-laravel/commit/6ccfa2ab23b7818cc22502470d56a226536aa913))
* **components:** add shadcn `Pagination` ([75cdf28](https://github.com/umaraziz0/reusables-laravel/commit/75cdf2838c7e88541df28a78276514f715ebc70a))
* **components:** add shadcn `Table` ([1b68b9c](https://github.com/umaraziz0/reusables-laravel/commit/1b68b9cffa602d1f3226a52bbed272293fb72c41))
* **components:** add shadcn `Table` and `Pagination` ([#111](https://github.com/umaraziz0/reusables-laravel/issues/111)) ([aecf738](https://github.com/umaraziz0/reusables-laravel/commit/aecf738a90f7ee160cd1a8c36fb3e0cf2a0c4fb4))


### Bug Fixes

* **components:** give `Table` and `Pagination` pages a single root element ([4c37d07](https://github.com/umaraziz0/reusables-laravel/commit/4c37d07ff0728848a14f223e86d2d9646988d80e))

## [0.8.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.7.1...reusables-laravel-v0.8.0) (2026-11-16)


### Features

* **user:** add soft deletes to users ([f742ac4](https://github.com/umaraziz0/reusables-laravel/commit/f742ac4b921d1f597065110cd1570560021f0db7))
* **user:** add soft deletes to users ([#107](https://github.com/umaraziz0/reusables-laravel/issues/107)) ([fe5dc7a](https://github.com/umaraziz0/reusables-laravel/commit/fe5dc7a62768a7b6273f97a672589a31be3604f7))

## [0.7.1](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.7.0...reusables-laravel-v0.7.1) (2026-11-16)


### Bug Fixes

* **console:** require a description in starter:init ([f73c649](https://github.com/umaraziz0/reusables-laravel/commit/f73c6494a30eea6eb6d8739f9c69cba74bcfb98d))
* **release:** stop bumping composer.json so releases keep composer.lock current ([28d9235](https://github.com/umaraziz0/reusables-laravel/commit/28d92352a60ac81b609eb05bece0b543d8ecc79c))
* **setup:** build the frontend in composer run setup ([34a9903](https://github.com/umaraziz0/reusables-laravel/commit/34a99035979c3203987faef2700d1289181d2339))
* template setup flow for new projects ([#105](https://github.com/umaraziz0/reusables-laravel/issues/105)) ([c5b9e00](https://github.com/umaraziz0/reusables-laravel/commit/c5b9e00ec2318e70ef14d69a593021a6b75d4330))

## [0.7.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.6.0...reusables-laravel-v0.7.0) (2026-11-16)


### Features

* **components:** add `SwitchField` and move component browser tests to Vitest ([c5ba228](https://github.com/umaraziz0/reusables-laravel/commit/c5ba228e83dc2d757538344b94358d5179ba0b0e))
* **components:** add shadcn `Switch` component ([ca0c3b6](https://github.com/umaraziz0/reusables-laravel/commit/ca0c3b6ad1f98038f3a281a9ffe54a9c288bbae8))
* **components:** create `SwitchField` component ([62c58d0](https://github.com/umaraziz0/reusables-laravel/commit/62c58d09f51b2e066c0d733b48b8920b6a2e9c57))
* **console:** add starter:init to turn a clone into a new project ([a19e9c4](https://github.com/umaraziz0/reusables-laravel/commit/a19e9c4446ffdf61549ec13a379c8a8869361a78))
* **lang:** add Indonesian translations ([3e397dd](https://github.com/umaraziz0/reusables-laravel/commit/3e397ddf151e7bf0db35887285a5e3d0dd8af078))
* **seeders:** seed roles and a local superadmin on setup ([5d6c12c](https://github.com/umaraziz0/reusables-laravel/commit/5d6c12c54d9f36babd41b4ba18321515d3f6a335))


### Bug Fixes

* **console:** lowercase the composer package name in starter:init ([633d66c](https://github.com/umaraziz0/reusables-laravel/commit/633d66cfb234122de0381e85f0c11d6d11a9008e))
* **console:** refresh the composer.lock hash after starter:init ([53be541](https://github.com/umaraziz0/reusables-laravel/commit/53be5413e0e1a5a2d67034871f85bc638f4d36e9))
* **health:** check only the queues Horizon works ([dcdb1df](https://github.com/umaraziz0/reusables-laravel/commit/dcdb1df237e85b94d548942638abbd298e6af9e3))
* **lang:** move vendor translations so lang/ is loaded ([8e81379](https://github.com/umaraziz0/reusables-laravel/commit/8e81379827002faf2468fddd3f760870ecfc4780))
* **rules:** translate the phone number field name ([d01bee0](https://github.com/umaraziz0/reusables-laravel/commit/d01bee03200e980ac5a8a3d23e0056d1eb1b97f3))
* **schedule:** schedule health heartbeats and telescope pruning ([4c8a249](https://github.com/umaraziz0/reusables-laravel/commit/4c8a249b17255c0c4986fb55579743ffadd1436b))

## [0.6.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.5.0...reusables-laravel-v0.6.0) (2026-11-14)


### Features

* **components:** add `CheckboxField` with boolean v-model ([2ad9b78](https://github.com/umaraziz0/reusables-laravel/commit/2ad9b780fa582c9532f9161482a7fc9e2976efd2))
* **components:** add `CheckboxField` with boolean v-model ([1d90fb8](https://github.com/umaraziz0/reusables-laravel/commit/1d90fb8df94dcbea18e1b68f862cf3de1a671cbf))
* **components:** add `RadioField` built on shadcn `RadioGroup` ([9d2c0f3](https://github.com/umaraziz0/reusables-laravel/commit/9d2c0f377738546562b143688a7adce1060ad691))
* **components:** add `RadioField` built on shadcn `RadioGroup` ([1900a2d](https://github.com/umaraziz0/reusables-laravel/commit/1900a2dc37bf17ec30c57c42d40332d49ede5c0e))
* **components:** add shadcn `radio-group` ([135b5b0](https://github.com/umaraziz0/reusables-laravel/commit/135b5b0d0f27b1a0245e831d31b0994a2a442684))


### Bug Fixes

* **components:** link `RadioField` option descriptions to their radios ([5fefb23](https://github.com/umaraziz0/reusables-laravel/commit/5fefb2309763e1f63ff38b6cae033a811939985f))

## [0.5.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.4.0...reusables-laravel-v0.5.0) (2026-11-14)


### Features

* **components:** add `TextareaField` with optional character counter ([e5b541b](https://github.com/umaraziz0/reusables-laravel/commit/e5b541b9a9c6575b31c80c26a4e4038e6ce58fab))
* **components:** add `TextareaField` with optional character counter ([7f0588e](https://github.com/umaraziz0/reusables-laravel/commit/7f0588e469d69c584aafc6f22d71c726e2f048c4))
* **components:** add shadcn-vue `Field` ([80e3cc5](https://github.com/umaraziz0/reusables-laravel/commit/80e3cc527db502ce199ea452edf9c76c0f25e32a))


### Bug Fixes

* **components:** give `InputField` a `disabled` prop so Field dims the label ([63de974](https://github.com/umaraziz0/reusables-laravel/commit/63de974f9d7d844d31ae9182d78d2a112152879e))
* **components:** mark form fields data-disabled so Field dims the label ([a90921c](https://github.com/umaraziz0/reusables-laravel/commit/a90921c3803559d9de665169f8076cb7632287ff))
* **security:** allow eval in CSP while New Debug Bar is active ([9081c42](https://github.com/umaraziz0/reusables-laravel/commit/9081c42e11a31e780f0fa86b8aee43088b218d08))
* **security:** allow eval in CSP while New Debug Bar is active ([cbcdf5a](https://github.com/umaraziz0/reusables-laravel/commit/cbcdf5a1c972314451869ce3d5d9ac57e0661d94))

## [0.4.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.3.0...reusables-laravel-v0.4.0) (2026-11-13)


### Features

* **components:** add `PhoneNumberField` ([c662e17](https://github.com/umaraziz0/reusables-laravel/commit/c662e173f10af44388f01081817debd4b69a8745))
* **components:** add `PhoneNumberField` preview page ([5c473ab](https://github.com/umaraziz0/reusables-laravel/commit/5c473abe2982731a71cbbb8e3a4d360bd9353f48))
* **components:** create custom `PhoneNumberField` component ([091a5e8](https://github.com/umaraziz0/reusables-laravel/commit/091a5e86ef578cdc44e0963505817ec6e67ec878))


### Bug Fixes

* **components:** forward form attribute to PhoneNumberField dial code ([8046e69](https://github.com/umaraziz0/reusables-laravel/commit/8046e69f6f71206474b264b2634db1a9a3344264))
* **components:** make dropdown trigger cursor pointer in `PhoneNumberField` ([586bdcd](https://github.com/umaraziz0/reusables-laravel/commit/586bdcdd8e75404633bc717bb6b56ea558167e84))
* **data:** correct Guyana's dial code and American Samoa's name ([0438a4b](https://github.com/umaraziz0/reusables-laravel/commit/0438a4b1314e4368dffbbdcdba8d9372d2742dc4))
* **security:** allow the Vite dev server in CSP whenever it is running ([77f4ea1](https://github.com/umaraziz0/reusables-laravel/commit/77f4ea1ebac288cf5c0e375ecadd3b61bbf17485))

## [0.3.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.2.0...reusables-laravel-v0.3.0) (2026-10-27)


### Features

* **components:** add `InputGroupField` ([8d25e3e](https://github.com/umaraziz0/reusables-laravel/commit/8d25e3ea17979623bc7e65276d46fc9b7871b219))
* **components:** add shadcn-vue `InputGroup` ([6180590](https://github.com/umaraziz0/reusables-laravel/commit/6180590653b49a655b7a718691f2fbd62e879677))
* **components:** create custom `InputGroupField` component ([415f11e](https://github.com/umaraziz0/reusables-laravel/commit/415f11eeb52f723929ee11178fc16009052bc931))


### Bug Fixes

* **components:** default `InputGroupButton` to type="button" ([cc2498d](https://github.com/umaraziz0/reusables-laravel/commit/cc2498d6ceeaa9d32fbf89db803976168393216e))
* **layout:** fix `ComponentsSidebar` to default open when child item is active ([058180a](https://github.com/umaraziz0/reusables-laravel/commit/058180a22c787e3a701b48102e34348e5e68c5a5))

## [0.2.0](https://github.com/umaraziz0/reusables-laravel/compare/reusables-laravel-v0.1.0...reusables-laravel-v0.2.0) (2026-10-21)


### Features

* **components:** add email spell checker to `InputField` ([f76c4be](https://github.com/umaraziz0/reusables-laravel/commit/f76c4be93929039fc3337f3f6fd89107e9b197ec))
* **components:** add SearchSelectField preview page ([77283cf](https://github.com/umaraziz0/reusables-laravel/commit/77283cf220d4f8129bd47fff624851962ac41ec1))
* **components:** add SelectField form component and preview page ([64d94af](https://github.com/umaraziz0/reusables-laravel/commit/64d94afb0d51bbbb19ec8f85092e676b9807ec8a))
* **components:** add virtualized SearchSelectField ([54c7d96](https://github.com/umaraziz0/reusables-laravel/commit/54c7d96ef5f53b9d0795bb647a4bcfc8076b4c65))
* **components:** add virtualized SearchSelectField form component ([b54ed89](https://github.com/umaraziz0/reusables-laravel/commit/b54ed89bcaa8e033b0a8b7bbe667712861ff6269))
* **config:** add package configuration and bootstrap wiring ([2d36992](https://github.com/umaraziz0/reusables-laravel/commit/2d369927896b772088910d42f3938b72b91180ff))
* **database:** add migrations, seeders and user search schema ([f260ebb](https://github.com/umaraziz0/reusables-laravel/commit/f260ebb7a5094e5b633817047098e30d2b3711ef))
* port reusable backend features ([2358ce1](https://github.com/umaraziz0/reusables-laravel/commit/2358ce1ccd3b89311c910061b412f4c0add996db))
* **ui:** port component library onto the newer starter kit UI ([02d5d5a](https://github.com/umaraziz0/reusables-laravel/commit/02d5d5a6945a72461e3e6a7d7e3aefee5b97bcb6))


### Bug Fixes

* **components:** add a11y attributes to `SearchSelectField` ([d6b5163](https://github.com/umaraziz0/reusables-laravel/commit/d6b5163a89c821848999003345cde1d45e3aaf80))
* **components:** reduce required indicator spacing on input fields ([7f44bac](https://github.com/umaraziz0/reusables-laravel/commit/7f44bac60cf658a7e8bf513b0f6dfb9a99ad68ee))
* **components:** remove regular Select field comparison ([870f1c8](https://github.com/umaraziz0/reusables-laravel/commit/870f1c8088936a587f95344b1a69be456b2a606d))
* **components:** rename & optimize `InputField` ([ee1b566](https://github.com/umaraziz0/reusables-laravel/commit/ee1b566adbd27633598fc95ac9d5acc7c9ad8379))
* **components:** update shadcn `Select` ([c5c3539](https://github.com/umaraziz0/reusables-laravel/commit/c5c35397ee1725a037ea773268d93d0fd52e7a06))
* **components:** update shadcn collapsible & accordion ([bb6c079](https://github.com/umaraziz0/reusables-laravel/commit/bb6c079ce0e37b2c5d281371b53edff8382ec496))
* **components:** update shadcn components ([d6eb513](https://github.com/umaraziz0/reusables-laravel/commit/d6eb5133b875d62557e671ffb60c2614387549b3))
* **layout:** fix components layout submenu item when sidebar retracted ([3d52bf6](https://github.com/umaraziz0/reusables-laravel/commit/3d52bf61b418f8372b55fbf96b50fbcdea248dac))
* **layout:** fix components sidebar collapsible ([f4af59a](https://github.com/umaraziz0/reusables-laravel/commit/f4af59a1a653eab336b0fab3e06c2f5120e0438d))
