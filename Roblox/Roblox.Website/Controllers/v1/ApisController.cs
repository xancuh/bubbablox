using Microsoft.AspNetCore.Mvc;
using System.IO;
using System.Threading.Tasks;

namespace Roblox.Website.Controllers;

[ApiController]
[Route("/apisite")]
public class ApisController : ControllerBase
{
    private const string ValidApiKey = "D6925E56-BFB9-4908-AAA2-A5B1EC4B2D79";
    private const string FilePath = @"C:\Users\Admin\Desktop\Revival\ecsr\fflags.json";

    [HttpGet("universal-app-configuration/v1/behaviors/robux-product-policy/content")]
    public dynamic GetRobuxProductPolicy()
    {
        return new
        {
            allowViews = new string[] { "RobuxPackage" },
        };
    }

    [HttpGet("universal-app-configuration/v1/behaviors/account-settings-ui/content")]
    public dynamic GetAccountSettingsStuff()
    {
        return new
        {
            displayTwoStepVerification = true,
            displayAccountPIN = true,
            displayEmailAddress = true,
            displayWeChat = false,
            displayQQ = false,
            displayConnectedAccounts = true,
            displayNotificationStream = true,
            displayDesktopPush = true,
            displayMobilePush = true,
            displayPhoneNumber = true,
            displaySocialMedia = true,
            displayDescription = true,
            displayAccountRestrictions = true,
            displayCountryList = true,
            displayWhoCanMessageMe = true,
            displayWhoCanFindMeByPhoneNumber = true,
            displayWhoCanInviteMeToVIPServers = true,
            displayWhoCanTradeWithMe = true,
            displayTradeQualityFilter = true,
            displayLanguageList = true,
            displayChangeUsername = true,
            displayChangePassword = true,
            displayBillingTab = true,
            displayPrivacyModeToolTip = true,
            displayIdentityVerification = true,
            displayVoiceChatSettings = true,
            displayPasswordRow = true
        };
    }

    [HttpGet("purchase-warning/v1/purchase-warnings/pre-purchase-authorization")]
    public dynamic GetPurchaseWarning()
    {
        return new
        {
            Action = "ParentalAuthorization13To17",
        };
    }

    [HttpGet("universal-app-configuration/v1/behaviors/group-details-ui/content")]
    public async Task<dynamic> GetGroupConfig()
    {
        return new
        {
            displayShout = true,
            displayDescription = true,
            displayMembers = true,
            displayWall = true,
            displayRank = true,
            checkGroupOrigin = false
        };
    }

    [HttpGet("universal-app-configuration/v1/behaviors/configure-group-ui/content")]
    public dynamic GetGroupConfigureData()
    {
        return new
        {
            displayUploadGroupIcon = true,
            displayJoinRequirementsSetting = true,
            displayGroupPrivacySettings = true,
            displayPlayerUsername = true,
            displayGroupFundsAndRobuxIcon = true,
        };
    }

    [HttpGet("clientsettings/Setting/QuietGet/ClientAppSettings/")]
    public async Task<IActionResult> GetClientAppSettings([FromQuery] string apiKey)
    {
        // Validate API key
        if (apiKey != ValidApiKey)
        {
            return Unauthorized(new { error = "Invalid API Key" });
        }

        // Check if file exists
        if (!System.IO.File.Exists(FilePath))
        {
            return NotFound(new { error = "File not found" });
        }

        // Read file contents
        string jsonContent = await System.IO.File.ReadAllTextAsync(FilePath);

        // Return JSON response
        return Content(jsonContent, "application/json");
    }
}
