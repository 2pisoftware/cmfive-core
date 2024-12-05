import { expect, test } from "@playwright/test";
import { ChannelsHelper } from "@utils/channels";
import { CmfiveHelper, GLOBAL_TIMEOUT } from "@utils/cmfive";

// test.describe.configure({mode: 'parallel'});

test("that you can create a Web Channel using the Channels module", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    // check that you can create a web channel
    const channel = {
        "Name": CmfiveHelper.randomID("channel_"),
        "Run processors?": false,
        "Web API URL": "url"
    }

    await ChannelsHelper.createWebChannel(page, channel);
    await ChannelsHelper.verifyWebChannel(page, channel);
    
    // check that you can edit a web channel
    const editedChannel = await ChannelsHelper.editWebChannel(page, channel, {
        "Name": channel["Name"]+"_edited",
        "Is Active": false,
        "Run processors?": true,
        "Web API URL": "url_edited"
    });
    await ChannelsHelper.verifyWebChannel(page, editedChannel);

    // check that you can create a processor
    await ChannelsHelper.createProcessor(page, isMobile, editedChannel["Name"]+"_processor", editedChannel["Name"] as string, "channels.TestProcessor");
    await ChannelsHelper.verifyProcessor(page, editedChannel["Name"]+"_processor", editedChannel["Name"] as string, "channels.TestProcessor");

    // check that you can delete a processor
    await ChannelsHelper.deleteProcessor(page, isMobile, editedChannel["Name"]+"_processor");
    await expect(page.getByText(editedChannel["Name"]+"_processor")).toBeHidden();

    // check that you can delete a web channel
    await ChannelsHelper.deleteChannel(page, isMobile, editedChannel);
});

test("that you can create an Email Channel using the Channels module", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    // check that you can create an email channel
    const channel = {
        "Name": CmfiveHelper.randomID("channel_"),
        "Protocol": "POP3",
        "Server URL": "url",
        "Username": "username",
        "Password": "password",
        "Is Active": false,
        "Verify Peer": false,
        "Allow self signed certificates": true,
    }

    await ChannelsHelper.createEmailChannel(page, channel);
    await ChannelsHelper.verifyEmailChannel(page, channel);

    // check that you can edit an email channel
    const editedChannel = await ChannelsHelper.editEmailChannel(page, channel, {
        "Name": channel["Name"]+"_edited",
        "Is Active": true,
        "Run processors?": false,
        "Protocol": "IMAP",
        "Server URL": "url_edited",
        "Username": "username_edited",
        "Password": "password_edited",
        "Port": "1234",
        "Use Auth?": false,
        "Verify Peer": true,
        "Allow self signed certificates": false,
        "Folder": "folder",
        "To": "to",
        "From": "from",
        "Subject": "subject",
        "Body": "body",
        "CC": "cc",
        "Post Read Action": "Archive",
        "Post Read Data": "post read data",
    });
    await ChannelsHelper.verifyEmailChannel(page, editedChannel);

    // check that you can delete an email channel
    await ChannelsHelper.deleteChannel(page, isMobile, editedChannel);
});