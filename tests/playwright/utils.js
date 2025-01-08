const { expect } = require('@wordpress/e2e-test-utils-playwright');
const dummyText = require('./assets/dummyText.json');

// not in use but it works. creates post and assign cats and tags
export const createRelatedArticleWithTerms = async function (
	utils,
	options = { title: 'New Article to refer', categories: [], tags: [], assert: true }
) {
	const { page, admin, editor } = utils;

	// Create a new post
	await admin.createNewPost({
		title: options.title,
		content: dummyText.content,
		showWelcomeGuide: false,
		fullscreenMode: false,
	});

	// Publishing
	await editor.publishPost();

	// asserting that the post has correctly been published
	if (options.assert)
		await expect(page.getByLabel('Editor publish').getByRole('link', { name: 'View Post' })).toBeVisible();

	return true;
};

// using WP CLI for quick data manipulation (not in use and not tested)
export const execS = async function (command) {
	const { exec } = require('child_process');
	return new Promise((resolve, reject) => {
		exec(command, (error, stdout, stderr) => {
			if (error) {
				reject(error);
			} else {
				resolve(stdout);
			}
		});
	});
};
