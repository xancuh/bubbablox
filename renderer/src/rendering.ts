import sharp = require("sharp");

// Poor man's anti-aliasing
export const resolutionMultiplier = {
	game: 4,
	asset: 1,
	userThumbnail: 2,
	userHeadshot: 2,
	mesh: 1,
	texture: 1
};

let uploadCallbacks: Record<string, Array<(args: any) => void>> = {};
export const doesCallbackExist = (id: string): boolean => {
  return uploadCallbacks[id] !== undefined;
}

export const getUploadCallbacks = () => uploadCallbacks;
export const registerCallback = (key: string, callback: (args: any) => void) => {
  if (!uploadCallbacks[key]) {
	uploadCallbacks[key] = [];
  }
  uploadCallbacks[key].push(callback);
  return () => {
	uploadCallbacks[key] = uploadCallbacks[key].filter(cb => cb !== callback);
	if (uploadCallbacks[key].length === 0) {
	  delete uploadCallbacks[key];
	}
  };
}

export const awaitResult = (key: string): Promise<void> => {
  return new Promise((res, rej) => {
	const timeout = setTimeout(() => {
	  rej('Timeout waiting for thumb');
	}, 1 * 60 * 1000);

	const unregister = registerCallback(key, () => {
	  clearTimeout(timeout);
	  unregister();
	  res();
	});
  });
}

export const getResult = (key: string, upscaleAmount: number): Promise<any> => {
  return new Promise((res, rej) => {
	const timeout = setTimeout(() => {
	  unregister();
	  rej(new Error('timeout waiting for thumb'));
	}, 2 * 60 * 1000);

	const unregister = registerCallback(key, async (data) => {
	  clearTimeout(timeout);
	  unregister();

	  try {
		if (typeof data.thumbnail === 'string') {
		  const originalImage = await sharp(Buffer.from(data.thumbnail, 'base64')).metadata();
		  if (typeof originalImage.width !== 'number' || typeof originalImage.height !== 'number') {
			throw new Error('bad image dimensions');
		  }

		  const image = await sharp(Buffer.from(data.thumbnail, 'base64'))
			.resize(
			  Math.trunc(originalImage.width / upscaleAmount),
			  Math.trunc(originalImage.height / upscaleAmount)
			)
			.png({ compressionLevel: 9, quality: 99, effort: 10 })
			.toBuffer();

		  data.thumbnail = image.toString('base64');
		}
		res(data);
	  } catch (error) {
		rej(error);
	  }
	});

	process.on('exit', () => {
	  clearTimeout(timeout);
	  unregister();
	});
  });
};