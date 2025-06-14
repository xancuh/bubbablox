--	// FileName: ChatSettings.lua
--	// Written by: Xsitsu
--	// Description: Settings module for configuring different aspects of the chat window.

local module = {}

---[[ Chat Behaviour Settings ]]
module.WindowDraggable = false
module.WindowResizable = false
module.ShowChannelsBar = false
module.GamepadNavigationEnabled = false
module.ShowUserOwnFilteredMessage = false	--Show a user the filtered version of their message rather than the original.
-- Make the chat work when the top bar is off
module.ChatOnWithTopBarOff = false

---[[ Chat Text Size Settings ]]
module.ChatWindowTextSize = 18
module.ChatChannelsTabTextSize = 18
module.ChatBarTextSize = 18
module.ChatWindowTextSizePhone = 14
module.ChatChannelsTabTextSizePhone = 18
module.ChatBarTextSizePhone = 14

---[[ Font Settings ]]
module.DefaultFont = Enum.Font.SourceSansBold
module.ChatBarFont = Enum.Font.SourceSansBold

----[[ Color Settings ]]
module.BackGroundColor = Color3.new(0, 0, 0)
module.DefaultMessageColor = Color3.new(1, 1, 1)
module.DefaultNameColor = Color3.new(1, 1, 1)
module.DefaultChannelColor = Color3.new(1, 1, 1)
module.ChatBarBackGroundColor = Color3.new(0, 0, 0)
module.ChatBarBoxColor = Color3.new(1, 1, 1)
module.ChatBarTextColor = Color3.new(0, 0, 0)
module.ChannelsTabUnselectedColor = Color3.new(0, 0, 0)
module.ChannelsTabSelectedColor = Color3.new(30/255, 30/255, 30/255)

---[[ Window Settings ]]
module.MinimumWindowSize = UDim2.new(0.3, 0, 0.25, 0)
module.MaximumWindowSize = UDim2.new(1, 0, 1, 0) -- if you change this to be greater than full screen size, weird things start to happen with size/position bounds checking.
module.DefaultWindowPosition = UDim2.new(0, 0, 0, 0)
local extraOffset = (7 * 2) + (5 * 2) -- Extra chatbar vertical offset
module.DefaultWindowSizePhone = UDim2.new(0.5, 0, 0.5, extraOffset)
module.DefaultWindowSizeTablet = UDim2.new(0.4, 0, 0.3, extraOffset)
module.DefaultWindowSizeDesktop = UDim2.new(0.3, 0, 0.25, extraOffset)

---[[ Fade Out and In Settings ]]
module.ChatWindowBackgroundFadeOutTime = 0.5 --Chat background will fade out after this many seconds.
module.ChatWindowTextFadeOutTime = 30				--Chat text will fade out after this many seconds.
module.ChatDefaultFadeDuration = 0.8
module.ChatShouldFadeInFromNewInformation = false

---[[ Channel Settings ]]
module.GeneralChannelName = "All" -- You can set to nil to turn off echoing to a general channel.
module.ChannelsBarFullTabSize = 4 -- number of tabs in bar before it starts to scroll
module.MaxChannelNameLength = 12
--// Although this feature is pretty much ready, it needs some UI design still.
module.RightClickToLeaveChannelEnabled = false
module.MessageHistoryLengthPerChannel = 50

---[[ Message Settings ]]
module.MaximumMessageLength = 200
module.DisallowedWhiteSpace = {"\n", "\r", "\t", "\v", "\f"}
module.ClickOnPlayerNameToWhisper = true

local ChangedEvent = Instance.new("BindableEvent")

local proxyTable = setmetatable({},
{
	__index = function(tbl, index)
		return module[index]
	end,
	__newindex = function(tbl, index, value)
		module[index] = value
		ChangedEvent:Fire(index, value)
	end,
})

rawset(proxyTable, "SettingsChanged", ChangedEvent.Event)

return proxyTable
