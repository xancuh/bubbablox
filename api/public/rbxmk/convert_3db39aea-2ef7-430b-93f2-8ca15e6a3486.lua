
			local input = './3db39aea-2ef7-430b-93f2-8ca15e6a3486.rbxm'
			local output = './3db39aea-2ef7-430b-93f2-8ca15e6a3486.rbxmx'
			local file = fs.read(input)
			fs.write(output, file, 'rbxmx')
		