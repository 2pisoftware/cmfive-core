import { test, expect } from '@playwright/test';
import { PlaywrightHelper } from './helpers';

test('test admin page', async ({ page }) => {
    await PlaywrightHelper.login(page, 'admin', 'admin');
    
});