// ImageController.php
public function uploadImage(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $imageName = time().'.'.$request->image->extension();
    $request->image->move(public_path('images'), $imageName);

    // Store $imageName in the database or perform other actions

    return response([
        'image_url' => 'images/' . $imageName,
    ], 200);
}
