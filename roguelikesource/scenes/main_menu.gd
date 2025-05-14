extends CanvasLayer

func _ready():
	Input.set_mouse_mode(Input.MOUSE_MODE_VISIBLE)
	$Control/CreditsPanel.visible = false

func _on_start_button_pressed():
	get_tree().change_scene_to_file("res://scenes/Main.tscn")

func _on_quit_button_pressed():
	get_tree().quit()  
	

func _on_credits_button_pressed():
	$Control/MainPanel.visible = false
	$Control/CreditsPanel.visible = true

func _on_back_button_pressed():
	$Control/MainPanel.visible = true
	$Control/CreditsPanel.visible = false
